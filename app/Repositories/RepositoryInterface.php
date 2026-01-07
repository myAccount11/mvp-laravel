<?php

namespace App\Repositories;

interface RepositoryInterface
{
    public function getModel();
    public function query();
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findBy(array $conditions = [], array $columns = ['*'], array $relations = [], array $order = []);
    public function findOneBy(array $conditions, array $columns = ['*'], array $relations = []);
    public function findAndCountAll(array $options = []): array;
}

