<?php

namespace App\Controllers;

use App\Models\RecordModel;
use CodeIgniter\Controller;

class RecordController extends Controller
{
    private $nodeApiUrl = 'http://localhost:3000/sync-mongodb';

    public function index()
    {
        $recordModel = new RecordModel();
        $data['records'] = $recordModel->findAll();

        return view('create_record', $data);
    }

    public function create()
    {
        if (!$this->validate([
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
        ])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed. Please check your input.'
            ]);
        }

        $recordModel = new RecordModel();

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        if ($recordModel->insert($data)) {
            $data['id'] = $recordModel->getInsertID();
            $this->syncWithMongoDB($data, 'create');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Record created in MySQL and MongoDB.',
                'data'    => $data
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create record in MySQL.'
            ]);
        }
    }

    public function update($id)
    {
        $recordModel = new RecordModel();
        $record = $recordModel->find($id);

        if (!$record) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Record not found.'
            ]);
        }

        $data = [
            'id' => $id,
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        if ($recordModel->update($id, $data)) {
            $this->syncWithMongoDB($data, 'update');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Record updated in MySQL and MongoDB.',
                'data'    => $data
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update record in MySQL.'
            ]);
        }
    }

    public function delete($id)
    {
        $recordModel = new RecordModel();
        $record = $recordModel->find($id);

        if (!$record) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Record not found.'
            ]);
        }

        if ($recordModel->delete($id)) {
            $this->syncWithMongoDB(['id' => $id], 'delete');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Record deleted from MySQL and MongoDB.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete record from MySQL.'
            ]);
        }
    }

    private function syncWithMongoDB($data, $operation)
    {
        $data['operation'] = $operation;
        $client = \Config\Services::curlrequest();

        try {
            $client->post($this->nodeApiUrl, [
                'headers' => ['Content-Type' => 'application/json'],
                'json'    => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'MongoDB Sync Error: ' . $e->getMessage());
        }
    }
}
