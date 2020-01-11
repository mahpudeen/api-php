<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

function checkApiKey($apiKey)
{
    if ($apiKey == "" or $apiKey == null) {
        return "Tidak Ada";
    } else {
        return "Ada";
    }
}

function checkApiKeyExpired($apiKey)
{
    $conn = new mysqli('localhost', 'root', '', 'modal_antara');
    $scriptSqlExpired = "SELECT * FROM user WHERE api_key = '$apiKey' AND expired_on > '" . date('Y-m-d H:i:s') . "'";
    $sqlCheckExpired = $conn->query($scriptSqlExpired);
    if ($sqlCheckExpired->num_rows == 0) {
        return "Expired";
    } else {
        return "No Expired";
    }
}


return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });

    //1. Get All User
    $app->get('/data_users/', function (Request $request, Response $response) {
        $sql = "SELECT * FROM tb_user";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch_all(MYSQLI_ASSOC);

        if ($result == null) {
            return $response->withJson(["status" => "failed", "message" => "users not found!"], 404);
        } else {
            return $response->withJson(["status" => "success", "data" => $result], 200);
        }
    });

    //2. Delete User
    $app->post('/del_user/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $id_user = $jsonParams['id_user'];
        //Cek ID User
        $check = $this->db->query("SELECT * FROM tb_user WHERE id_user = '$id_user'");
        $res = $check->num_rows;

        if ($res > 0) {
            $sql = $this->db->query("DELETE FROM tb_user WHERE id_user = '$id_user'");
            return $response->withJson(["status" => "delete success", "data" => $id_user], 200);
        } else {
            return $response->withJson(["status" => "failed", "message" => "users not found!"], 404);
        }
    });

    //3. Registrasi User
    $app->post('/regis_user/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $nama_lengkap = $jsonParams['nama_lengkap'];
        $email_user = $jsonParams['email_user'];
        $nomor_hp = $jsonParams['nomor_hp'];
        $password_user = $jsonParams['password_user'];

        $query = "INSERT INTO tb_user(nama_lengkap, email_user, password_user, nomor_hp, level_akses)
            VALUES ('$nama_lengkap', '$email_user', '$password_user', '$nomor_hp', '2')";
        
        if ($this->db->query($query)) {
            return $response->withJson(["status" => "success", "message" => "Register success!"], 200);
        } else {
            return $response->withJson(["status" => "failed", "message" => "Register failed!"], 404);
        }

    });

    //4. Login User
    $app->post('/login_user/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $email_user = $jsonParams['email_user'];
        $password_user = $jsonParams['password_user'];

        $query = "SELECT * FROM tb_user WHERE email_user='$email_user' AND password_user='$password_user'";
        $res = $this->db->query($query);
        $row = $res->num_rows;
        if ($row != null) {
            return $response->withJson(["status" => "success", "message" => "Login Sukses!"], 200);
        } else {
            return $response->withJson(["status" => "failed", "message" => "Email atau Password Salah!"], 404);
        }

    });










































    /*** Customer ***/

    //1. Get All Customer
    $app->get("/customers/", function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $key = $jsonParams['api_key'];

        //Cek ID User
        $sqlIdUser = $this->db->query("SELECT id FROM user WHERE api_key = '$key'");
        $rowIdUser = $sqlIdUser->fetch_array();
        $idUser = $rowIdUser[0];

        if (checkApiKey($key) == "Ada") {
            if (checkApiKeyExpired($key) == 'Expired') {
                return $response->withJson(["status" => "failed", "message" => "API Key expired or not found!"], 401);
            } else {
                $sql = "SELECT * FROM customers WHERE id_user = $idUser";
                $stmt = $this->db->query($sql);
                $result = $stmt->fetch_all(MYSQLI_ASSOC);

                if ($result == null) {
                    return $response->withJson(["status" => "failed", "message" => "Customer data not found in this user!"], 404);
                } else {
                    return $response->withJson(["status" => "success", "data" => $result], 200);
                }
            }
        } else {
            return $response->withJson(["message" => "API Key Invalid!"], 404);
        }
    });

    //2. Get Customer per NIK
    $app->get('/customers/nik/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $key = $jsonParams['api_key'];

        //Cek ID User
        $sqlIdUser = $this->db->query("SELECT id FROM user WHERE api_key = '$key'");
        $rowIdUser = $sqlIdUser->fetch_array();
        $idUser = $rowIdUser[0];

        if (checkApiKey($key) == "Ada") {
            if (checkApiKeyExpired($key) == 'Expired') {
                return $response->withJson(["status" => "failed", "message" => "API Key expired or not found!"], 401);
            } else {
                $nik = $jsonParams["nik"];

                if (strlen($nik) < 16) {
                    return $response->withJson(["status" => "NIK must be 16 digits"], 404);
                } else {
                    if ($nik == "" or $nik == null) {
                        return $response->withJson(["status" => "NIK empty!"], 404);
                    } else {
                        $sql = "SELECT * FROM customers WHERE nik = '$nik' AND id_user = $idUser";
                        $stmt = $this->db->query($sql);

                        if ($stmt->num_rows == 0) {
                            return $response->withJson(["status" => "failed", "message" => "Customer data not found!"], 404);
                        } else {
                            $result = $stmt->fetch_all(MYSQLI_ASSOC);
                            return $response->withJson(["status" => "success", "data" => $result], 200);
                        }
                    }
                }
            }
        } else {
            return $response->withJson(["message" => "API Key Invalid!"], 404);
        }
    });

    //3. Post and Edit Customer
    $app->post('/customers/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $key = $jsonParams['api_key'];

        //Cek ID User
        $sqlIdUser = $this->db->query("SELECT id FROM user WHERE api_key = '$key'");
        $rowIdUser = $sqlIdUser->fetch_array();
        $idUser = $rowIdUser[0];

        if (checkApiKey($key) == "Ada") {
            if (checkApiKeyExpired($key) == 'Expired') {
                return $response->withJson(["status" => "failed", "message" => "API Key expired or not found!"], 401);
            } else {
                //Json Parameter
                $nik = $jsonParams['nik'];

                // Cek NIK if exist
                $sqlCheckNIK = $this->db->query("SELECT * FROM customers WHERE nik = '$nik' AND id_user = $idUser");
                $jumlahRowCheckNIK = $sqlCheckNIK->num_rows;


                if ($jumlahRowCheckNIK == 0) # Jika NIK tidak ada, maka CREATE
                {
                    $nama = $jsonParams['nama'];
                    $jenis_kelamin = $jsonParams['jenis_kelamin'];
                    $alamat = $jsonParams['alamat'];
                    $provinsi = $jsonParams['provinsi'];
                    $kota = $jsonParams['kota'];
                    $kecamatan = $jsonParams['kecamatan'];
                    $kelurahan = $jsonParams['kelurahan'];
                    $kode_pos = $jsonParams['kode_pos'];
                    $tempat_lahir = $jsonParams['tempat_lahir'];
                    $tanggal_lahir = $jsonParams['tanggal_lahir'];
                    $no_telepon = $jsonParams['no_telepon'];
                    $no_hp = $jsonParams['no_hp'];
                    $email = $jsonParams['email'];
                    $agama = $jsonParams['agama'];
                    $status_pernikahan = $jsonParams['status_pernikahan'];
                    $pendidikan = $jsonParams['pendidikan'];
                    $pendapatan = $jsonParams['pendapatan'];
                    $bidang_pekerjaan = $jsonParams['bidang_pekerjaan'];
                    $pekerjaan = $jsonParams['pekerjaan'];

                    $sql = "INSERT INTO customers(nik, nama, jenis_kelamin, alamat, provinsi, kota, kecamatan, kelurahan, kode_pos, tempat_lahir, tanggal_lahir, no_telepon, no_hp, email, agama, status_pernikahan, pendidikan, bidang_pekerjaan, pekerjaan, pendapatan, id_user) 
                    VALUES ('$nik', '$nama', '$jenis_kelamin', '$alamat', '$provinsi', '$kota', '$kecamatan', '$kelurahan', '$kode_pos', '$tempat_lahir', '$tanggal_lahir', '$no_telepon', '$no_hp', '$email', '$agama', '$status_pernikahan', '$pendidikan', '$bidang_pekerjaan', '$pekerjaan', '$pendapatan', $idUser)";

                    if ($this->db->query($sql)) {
                        return $response->withJson(["status" => "success", "message" => "New Customer has been created successfully!"], 200);
                    }
                } else # Jika NIK tidak ada, maka EDIT
                {
                    $sqlFetchIDCustomer = $this->db->query("SELECT * FROM customers WHERE nik = '$nik' AND id_user = $idUser");
                    $rowFetchIDCustomer = $sqlFetchIDCustomer->fetch_assoc();
                    $idCustomer = $rowFetchIDCustomer['id'];

                    $updatedAt = date("Y-m-d H:i:s");
                    $nama = $jsonParams['nama'];
                    $jenis_kelamin = $jsonParams['jenis_kelamin'];
                    $alamat = $jsonParams['alamat'];
                    $provinsi = $jsonParams['provinsi'];
                    $kota = $jsonParams['kota'];
                    $kecamatan = $jsonParams['kecamatan'];
                    $kelurahan = $jsonParams['kelurahan'];
                    $kode_pos = $jsonParams['kode_pos'];
                    $tempat_lahir = $jsonParams['tempat_lahir'];
                    $tanggal_lahir = $jsonParams['tanggal_lahir'];
                    $no_telepon = $jsonParams['no_telepon'];
                    $no_hp = $jsonParams['no_hp'];
                    $email = $jsonParams['email'];
                    $agama = $jsonParams['agama'];
                    $status_pernikahan = $jsonParams['status_pernikahan'];
                    $pendidikan = $jsonParams['pendidikan'];
                    $pendapatan = $jsonParams['pendapatan'];
                    $bidang_pekerjaan = $jsonParams['bidang_pekerjaan'];
                    $pekerjaan = $jsonParams['pekerjaan'];

                    $sql = "UPDATE customers SET nik = '$nik', nama = '$nama', jenis_kelamin = '$jenis_kelamin', alamat = '$alamat', provinsi = '$provinsi', kota = '$kota', kecamatan = '$kecamatan', kelurahan = '$kelurahan', kode_pos = '$kode_pos', tempat_lahir = '$tempat_lahir', tanggal_lahir = '$tanggal_lahir', no_telepon = '$no_telepon', no_hp = '$no_hp', email = '$email', agama = '$agama', status_pernikahan = '$status_pernikahan', pendidikan = '$pendidikan', bidang_pekerjaan = '$bidang_pekerjaan', pekerjaan = '$pekerjaan', pendapatan = '$pendapatan', updated_at = '$updatedAt' WHERE id = $idCustomer";

                    if ($this->db->query($sql)) {
                        return $response->withJson(["status" => "success", "message" => "New Customer has been edited successfully!"], 200);
                    }
                }
            }
        } else {
            return $response->withJson(["message" => "API Key Invalid or Not Found!"], 404);
        }
    });

    //4. Edit Customers
    $app->put('/customers/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $key = $jsonParams['api_key'];

        //Cek ID User
        $sqlIdUser = $this->db->query("SELECT id FROM user WHERE api_key = '$key'");
        $rowIdUser = $sqlIdUser->fetch_array();
        $idUser = $rowIdUser[0];

        if (checkApiKey($key) == "Ada") {
            if (checkApiKeyExpired($key) == 'Expired') {
                return $response->withJson(["status" => "failed", "message" => "API Key expired or not found!"], 401);
            } else {
                //Json Parameter
                $nik = $jsonParams['nik'];

                //Cek NIK if exist
                $sqlCheckNIK = $this->db->query("SELECT * FROM customers WHERE nik = '$nik' AND id_user = $idUser");
                $jumlahRowCheckNIK = $sqlCheckNIK->num_rows;
                if ($jumlahRowCheckNIK == 0) {
                    return $response->withJson(["status" => "failed", "message" => "Customer data is not available!"], 404);
                } else {
                    $nama = $jsonParams['nama'];
                    $jenis_kelamin = $jsonParams['jenis_kelamin'];
                    $alamat = $jsonParams['alamat'];
                    $provinsi = $jsonParams['provinsi'];
                    $kota = $jsonParams['kota'];
                    $kecamatan = $jsonParams['kecamatan'];
                    $kelurahan = $jsonParams['kelurahan'];
                    $kode_pos = $jsonParams['kode_pos'];
                    $tempat_lahir = $jsonParams['tempat_lahir'];
                    $tanggal_lahir = $jsonParams['tanggal_lahir'];
                    $no_telepon = $jsonParams['no_telepon'];
                    $no_hp = $jsonParams['no_hp'];
                    $email = $jsonParams['email'];
                    $agama = $jsonParams['agama'];
                    $status_pernikahan = $jsonParams['status_pernikahan'];
                    $pendidikan = $jsonParams['pendidikan'];
                    $pendapatan = $jsonParams['pendapatan'];
                    $bidang_pekerjaan = $jsonParams['bidang_pekerjaan'];
                    $pekerjaan = $jsonParams['pekerjaan'];

                    $sql = "UPDATE customers SET nama = '$nama', jenis_kelamin = '$jenis_kelamin', alamat = '$alamat', provinsi = '$provinsi', kota = '$kota', kecamatan = '$kecamatan', kelurahan = '$kelurahan', kode_pos = '$kode_pos', tempat_lahir = '$tempat_lahir', tanggal_lahir = '$tanggal_lahir', no_telepon = '$no_telepon', no_hp = '$no_hp', email = '$email', agama = '$agama', status_pernikahan = '$status_pernikahan', pendidikan = '$pendidikan', bidang_pekerjaan = '$bidang_pekerjaan', pekerjaan = '$pekerjaan', pendapatan = '$pendapatan' WHERE nik = '$nik' AND id_user = $idUser";
                    if ($this->db->query($sql)) {
                        return $response->withJson(["status" => "success", "message" => "Customer data with NIK $nik has been edited successfully"], 200);
                    }
                }
            }
        } else {
            return $response->withJson(["message" => "API Key Invalid!"], 404);
        }

        return $response->withJson(["status" => "failed", "message" => "Failed to edit customer!"], 404);
    });

    /*** User ***/
    //1. Get All User
    // $app->get('/users/', function (Request $request, Response $response){
    //     $jsonParams = $request->getParsedBody();
    //     $key = $jsonParams['api_key'];

    //     if(checkApiKey($key) == "Ada")
    //     {
    //         if(checkApiKeyExpired($key) == 'Expired')
    //         {
    //             return $response->withJson(["status" => "failed", "message" => "API Key expired or not found!"], 401);
    //         }
    //         else
    //         {
    //             $sql = $this->db->query("SELECT * FROM user");
    //             $result = $sql->fetch_all(MYSQLI_ASSOC);
    //             return $response->withJson(["status" => "success", "data" => $result], 200);
    //         }
    //     }
    //     else
    //     {
    //         return $response->withJson(["message" => "API Key Invalid!"], 404);
    //     }
    // });

    //2. Get User per API Key
    $app->get('/users/details/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();

        $username = $jsonParams['username'];
        $password = $jsonParams['password'];

        $sql = $this->db->query("SELECT * FROM user WHERE username = '$username' AND password = '$password'");
        $data = $sql->fetch_assoc();
        return $response->withJson(["status" => "success", "data" => ['api_key' => $data['api_key'], 'api_secret' => $data['api_secret']]], 200);
    });

    //3. Post New User
    $app->post("/users/", function (Request $request, Response $response) {
        date_default_timezone_set("Asia/Jakarta");
        $jsonParams = $request->getParsedBody();

        //Cek username
        $sqlCheck = $this->db->query("SELECT COUNT(*) AS hitung FROM user WHERE username = '" . $jsonParams['username'] . "'");
        $rowCheck = $sqlCheck->fetch_array();
        $jumlahBarisCheck = $rowCheck[0];
        if ($jumlahBarisCheck == 1) {
            return $response->withJson(["status" => "failed", "message" => "Username has been used. Try another username!"], 403);
        } else {
            $nama = $jsonParams['nama'];
            $username = $jsonParams['username'];
            $password = $jsonParams['password'];
            //            $apiKey = md5($username).md5($password);
            $apiKey = md5($username . "danadarahasianyadisinipunyanyafineoz");
            //            $apiKey = md5($username);
            //            $apiSecret = md5($username).md5($password).md5($apiKey);
            $apiSecret = null;

            //        if($this->db->query("INSERT INTO user(nama, username, password, api_key, api_secret) VALUES ('$nama', '$username', '$password', '$apiKey', '$apiSecret')"))

            //Date +1 month
            $dateNow = date("Y-m-d");
            $clockNow = date("H:i:s");
            $currentMonth = date("m", strtotime($dateNow));
            $nextMonth = date("m", strtotime($dateNow . "+1 month"));
            if ($currentMonth == $nextMonth - 1) {
                $nextDate = date('Y-m-d', strtotime($dateNow . " +1 month"));
            } else {
                $nextDate = date('Y-m-d', strtotime("last day of next month", strtotime($dateNow)));
            }

            $expiredOn = $nextDate . " " . $clockNow;

            if ($this->db->query("INSERT INTO user(nama, username, password, api_key, expired_on, role) VALUES ('$nama', '$username', '$password', '$apiKey', '$expiredOn', 'user')")) {
                return $response->withJson(["status" => "success", "message" => "New user has been created successfully"], 200);
            } else {
                return $response->withJson(["status" => "failed", "message" => "Failed to create new user"], 400);
            }
        }
    });

    /*** Tembak Scoring ***/
    $app->post('/score/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $key = $jsonParams['api_key'];

        if (checkApiKey($key) == "Ada") {
            if (checkApiKeyExpired($key) == 'Expired') {
                return $response->withJson(["status" => "failed", "message" => "API Key expired or not found!"], 401);
            } else {
                //Cek ID User
                $sqlIdUser = $this->db->query("SELECT id FROM user WHERE api_key = '$key'");
                $rowIdUser = $sqlIdUser->fetch_array();
                $idUser = $rowIdUser[0];

                //Cek Customers
                $sqlCekCustomer = "SELECT COUNT(*) FROM customers WHERE id_user = '$idUser' AND nik = '" . $jsonParams['nik'] . "'";
                $stmtCekCustomer = $this->db->query($sqlCekCustomer);
                $resultCekCustomer = $stmtCekCustomer->fetch_array();

                //Get ID Customers
                $sqlGetIDCustomer = "SELECT id FROM customers WHERE id_user = $idUser AND nik = '" . $jsonParams['nik'] . "'";
                $stmtGetIDCustomer = $this->db->query($sqlGetIDCustomer);
                $resultGetIDCustomer = $stmtGetIDCustomer->fetch_array();

                if ($resultCekCustomer[0] == 1) {
                    //cURL
                    $url = "http://localhost:2020/predict";
                    $ch = curl_init($url);

                    $jsonData = array(
                        "tanggal_lahir" => $jsonParams['tanggal_lahir'],
                        "pengambilan_kredit" => (int) $jsonParams['pengambilan_kredit'],
                        "pengalaman_kerja" => (int) $jsonParams['pengalaman_kerja'],
                        "jabatan_id" => (int) $jsonParams['jabatan_id'],
                        "pendapatan" => (int) $jsonParams['pendapatan'],
                        "jumlah_tanggungan" => (int) $jsonParams['jumlah_tanggungan'],
                        "pendidikan" => (int) $jsonParams['pendidikan'],
                        "kepemilikan" => (int) $jsonParams['kepemilikan'],
                        "pinjaman" => (int) $jsonParams['pinjaman'],
                        "tenor_bulanan" => (int) $jsonParams['tenor_bulanan'],
                        "id_user" => (int) $idUser,
                        "id_customer" => $resultGetIDCustomer[0]
                    );

                    $jsonDataEncoded = json_encode($jsonData);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    $result = curl_exec($ch);
                    curl_close($ch);

                    $obj = json_decode($result);
                    //                    return $response->withJson(["status" => "success", "data" => ["Probability Kredit Lancar" => $obj['Probability Kredit Lancar'], "Grading" => $obj['Grading']]])->withStatus(200)->withHeader('Content-type', 'application/json');

                    return true;
                } else {
                    return $response->withJson(["status" => "failed", "message" => "Customer not found in this user!"], 404);
                }
            }
        } else {
            return $response->withJson(["message" => "API Key Invalid or not found!"], 404);
        }
    });
};
