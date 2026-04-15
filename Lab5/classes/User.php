<?php
class User
{
    public ?int $id = null;
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $password = '';
    public string $gender = '';
    public string $department = '';
    public string $skills = 'None';
    public string $profileImage = 'default.png';

    public function __construct(array $data = [])
    {
        if ($data === []) {
            return;
        }

        $this->id = isset($data['id']) ? (int) $data['id'] : null;
        $this->firstName = $data['first_name'] ?? $data['firstName'] ?? '';
        $this->lastName = $data['last_name'] ?? $data['lastName'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->gender = $data['gender'] ?? '';
        $this->department = $data['department'] ?? '';
        $this->skills = $data['skills'] ?? 'None';
        $this->profileImage = $data['profile_image'] ?? $data['profileImage'] ?? 'default.png';
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function setSkills(array $skills): void
    {
        $this->skills = count($skills) > 0 ? implode(', ', $skills) : 'None';
    }
}
