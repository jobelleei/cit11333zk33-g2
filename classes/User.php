<?php
// ============================================================
//  classes/User.php
//
//  User model for the users table.
//  It extends BaseModel so it can use getAll(), find(),
//  and delete() from BaseModel.
// ============================================================

class User extends BaseModel {
    protected $table = 'users';

    public function findByUsername($username) {
        return $this->db
                    ->table($this->table)
                    ->select()
                    ->where('username', $username)
                    ->first();
    }
}