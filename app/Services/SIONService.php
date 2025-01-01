<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SIONService {
    private $apiUrl;
    private $hashKey;
    private $client;

    public function __construct() {
        $this->apiUrl = env('SION_API_URL');
        $this->hashKey = env('SION_HASH_KEY');
        $this->client = new Client();
    }

    /**
     * Generate hash code from given data
     *
     * @param array|string $data
     *
     * @return string
     */
    public function generateHashCode($data)
    {
        $dataString = implode('', $data) . $this->hashKey;
        $hashCode = strtoupper(hash('sha256', $dataString));
        return $hashCode;
    }

    /**
     * Get mahasiswa profile
     *
     * @param string $identity
     *
     * @return array|false
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMahasiswaProfile($identity)
    {
        $hashCode = $this->generateHashCode([$identity]);
        $url = $this->apiUrl . '/mahasiswa/' . urlencode($identity) . '&' . $hashCode;

        try {
            $response = $this->client->get($url);
            $status = $response->getStatusCode();

            if ($status === 200) {
                $body = $response->getBody();
                $data = json_decode($body, true);
                $data = $data['profile'];
                return $data;
            }

            return false;
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get daftar mahasiswa
     *
     * @param string $tahun tahun akademik
     * @param string $jurusan jurusan
     * @param string $prodi program studi
     *
     * @return array|false
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMahasiswa($tahun, $jurusan, $prodi)
    {
        $hashCode = $this->generateHashCode([$tahun, $jurusan, $prodi]);

        $url = $this->apiUrl . '/Mahasiswa';

        $request = [
            'TahunAkademik' => $tahun,
            'Jurusan' => $jurusan,
            'Prodi' => $prodi,
            'HashCode' => $hashCode,
        ];

        try {
            $response = $this->client->post($url, [
                'body' => json_encode($request), 
                'timeout' => 5,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                $body = $response->getBody();
                $data = json_decode($body, true);
                $data = $data['daftar'];
                return $data;
            }

            return false;
        } catch (GuzzleException $e) {
            return false;
        }
    }

    /**
     * Get daftar dosen.
     *
     * @param string $tahun Academic year
     * @param string $jurusan Department
     * @param string $prodi Study program
     *
     * @return array|false Returns an array of lecturers if successful, or false on failure
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDosen($tahun, $jurusan, $prodi)
    {
        $hashCode = $this->generateHashCode([$tahun, $jurusan, $prodi]);

        $url = $this->apiUrl . '/Daftardosen';

        $request = [
            'TahunAkademik' => $tahun,
            'Jurusan' => $jurusan,
            'Prodi' => $prodi,
            'HashCode' => $hashCode,
        ];

        try {
            $response = $this->client->post($url, [
                'body' => json_encode($request), 
                'timeout' => 5,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                $body = $response->getBody();
                $data = json_decode($body, true);
                $data = $data['daftar'];
                return $data;
            }

            return false;
        } catch (GuzzleException $e) {
            return false;
        }
    }
}