<?php
require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/User.php';

class UserRepository
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getAll(): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM users ORDER BY id ASC');
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];

        while ($row = $result->fetch_assoc()) {
            $users[] = new User($row);
        }

        $stmt->close();

        return $users;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user ? new User($user) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user ? new User($user) : null;
    }

    public function save(User $user, bool $preservePassword = false): bool
    {
        if ($user->id !== null) {
            return $this->update($user, $preservePassword);
        }

        return $this->create($user);
    }

    private function create(User $user): bool
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO users (first_name, last_name, email, password, gender, department, skills, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'ssssssss',
            $user->firstName,
            $user->lastName,
            $user->email,
            $user->password,
            $user->gender,
            $user->department,
            $user->skills,
            $user->profileImage
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    private function update(User $user, bool $preservePassword = false): bool
    {
        if ($preservePassword) {
            $stmt = $this->connection->prepare(
                'UPDATE users SET first_name = ?, last_name = ?, email = ?, gender = ?, department = ?, skills = ?, profile_image = ? WHERE id = ?'
            );
            $stmt->bind_param(
                'sssssssi',
                $user->firstName,
                $user->lastName,
                $user->email,
                $user->gender,
                $user->department,
                $user->skills,
                $user->profileImage,
                $user->id
            );
        } else {
            $stmt = $this->connection->prepare(
                'UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?, gender = ?, department = ?, skills = ?, profile_image = ? WHERE id = ?'
            );
            $stmt->bind_param(
                'ssssssssi',
                $user->firstName,
                $user->lastName,
                $user->email,
                $user->password,
                $user->gender,
                $user->department,
                $user->skills,
                $user->profileImage,
                $user->id
            );
        }

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function saveProfileImage(array $file, string $currentImage = 'default.png'): string
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return $currentImage;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($extension, $allowedExtensions, true)) {
            throw new InvalidArgumentException('Only JPG, PNG, and GIF files are allowed.');
        }

        if (!is_dir(Config::UPLOAD_DIR) && !mkdir(Config::UPLOAD_DIR, 0755, true) && !is_dir(Config::UPLOAD_DIR)) {
            throw new RuntimeException('Unable to create upload directory.');
        }

        $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $targetPath = Config::UPLOAD_DIR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Failed to save uploaded profile image.');
        }

        return $filename;
    }
}
