<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function updateUser(User $user, array $data): bool
    {
        if (isset($data['role']) && !$user->hasRole($data['role'])) {
            $this->validateRoleChange($user, $data['role']);
        }

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
            ]);

            if (isset($data['role']) && !$user->hasRole($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteUser(User $user): bool
    {
        $this->validateUserDeletion($user);

        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function validateRoleChange(User $user, string $newRole): void
    {
        if ($user->hasRole('teacher') && $newRole !== 'teacher') {
            if ($this->hasActiveCourses($user)) {
                throw new \Exception('Cannot change role because user has active courses');
            }
        }

        if ($user->hasRole('student') && $newRole === 'teacher') {
            if ($this->hasActiveEnrollments($user)) {
                throw new \Exception('Cannot change role because user has active enrollments');
            }
        }
    }

    private function validateUserDeletion(User $user): void
    {
        if ($user->hasRole('teacher')) {
            if ($this->hasActiveCourses($user)) {
                throw new \Exception('Cannot delete user because user has active courses');
            }
        }

        if ($user->hasRole('student')) {
            if ($this->hasActiveEnrollments($user)) {
                throw new \Exception('Cannot delete user because user has active enrollments');
            }
        }

        if ($user->quizAttempts()->exists()) {
            throw new \Exception('Cannot delete user because user has quiz attempt history');
        }
    }

    private function hasActiveCourses(User $user): bool
    {
        return $user->teachingCourses()->exists();
    }

    private function hasActiveEnrollments(User $user): bool
    {
        return $user->enrolledCourses()->exists();
    }
}