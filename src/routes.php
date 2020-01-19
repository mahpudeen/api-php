<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

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
        $data = $res->fetch_assoc();
        $row = $res->num_rows;
        if ($row != null) {
            return $response->withJson(["status" => "success", "message" => "Login Sukses!", "data" => $data], 200);
        } else {
            return $response->withJson(["status" => "failed", "message" => "Email atau Password Salah!"], 404);
        }
    });

    //5. Daftar Menjadi Peserta
    $app->post('/daftar_jadi_peserta/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $id_user = $jsonParams['id_user'];

        $sqlCheck = "SELECT * FROM tb_user WHERE id_user='$id_user'";
        $res = $this->db->query($sqlCheck);
        $row = $res->num_rows;
        if ($row != null) {
            $nomor_identitas = $jsonParams['nomor_identitas'];
            $jenis_kelamin = $jsonParams['jenis_kelamin'];
            $alamat_user = $jsonParams['alamat_user'];
            $tempat_lahir = $jsonParams['tempat_lahir'];
            $tanggal_lahir = $jsonParams['tanggal_lahir'];
            $golongan_darah = $jsonParams['golongan_darah'];
            $riwayat_kesehatan = $jsonParams['riwayat_kesehatan'];
            $riwayat_kesehatan_keluarga = $jsonParams['riwayat_kesehatan_keluarga'];
            $obat_pribadi = $jsonParams['obat_pribadi'];
            $size_chart = $jsonParams['size_chart'];
			$tgl_pendaftaran = $jsonParams['tgl_pendaftaran'];
            $total_bayar = 200000+(intval($id_user));

            $query = "INSERT INTO tb_pendaftaran(id_user, nomor_identitas, jenis_kelamin, alamat_user, tempat_lahir, tanggal_lahir, golongan_darah, riwayat_kesehatan, riwayat_kesehatan_keluarga, obat_pribadi, size_chart, total_bayar, status_bayar, status_racepack, tgl_pendaftaran)
            VALUES ('$id_user', '$nomor_identitas', '$jenis_kelamin', '$alamat_user', '$tempat_lahir', '$tanggal_lahir', '$golongan_darah', '$riwayat_kesehatan', '$riwayat_kesehatan_keluarga', '$obat_pribadi', '$size_chart', $total_bayar, 'pending', 'N', '$tgl_pendaftaran')";

            if ($this->db->query($query)) {
                return $response->withJson(["status" => "success", "message" => "Daftar sukses!"], 200);
            } else {
                return $response->withJson(["status" => "failed", "message" => "Daftar gagal!"], 404);
            }
        } else {
            return $response->withJson(["status" => "failed", "message" => "User tidak terdaftar!"], 404);
        }
    });

    //5. Sudah Daftar Peseta Belum?
    $app->get('/cekDaftar/', function (Request $request, Response $response) {
        $id_user = $request->getParam("id_user");

        $sql = $this->db->query("SELECT * FROM tb_pendaftaran WHERE id_user='$id_user'");
        $data = $sql->fetch_assoc();
        $row = $sql->num_rows;
        if ($row > 0) {
            return $response->withJson(["statusDaftar" => true, "data" => $data], 200);
        }
        else {
            return $response->withJson(["statusDaftar" => false, "data" => $data], 200);
        }
    });

    //5. Pembayaran
    $app->get('/data_detail/', function (Request $request, Response $response) {
        $id_user = $request->getParam("id_user");

        $sql = $this->db->query("SELECT * FROM tb_pendaftaran p join tb_user u on p.id_user = u.id_user WHERE p.id_user='$id_user'");
        $data = $sql->fetch_assoc();
        $row = $sql->num_rows;
        if ($row > 0) {
            return $response->withJson(["statusDaftar" => true, "data" => $data], 200);
        }
        else {
            return $response->withJson(["statusDaftar" => false, "data" => $data], 200);
        }
    });

    $app->get('/landing_admin/', function (Request $request, Response $response) {
        $sql = $this->db->query("SELECT count(*) as jml_user FROM tb_user");
        $jml_user = $sql->fetch_assoc();
        $sql2 = $this->db->query("SELECT count(*) as jml_peserta FROM tb_pendaftaran");
        $jml_peserta = $sql2 ->fetch_assoc();
        $sql3 = $this->db->query("SELECT count(*) as jml_belumbayar FROM tb_pendaftaran WHERE status_bayar = 'pending'");
        $jml_belumbayar = $sql3 ->fetch_assoc();
        $sql4 = $this->db->query("SELECT count(*) as jml_lunas FROM tb_pendaftaran WHERE status_bayar = 'lunas'");
        $jml_lunas = $sql4->fetch_assoc();
        $sql5 = $this->db->query("SELECT count(*) as jml_racepack_n  FROM tb_pendaftaran WHERE status_racepack = 'N'");
        $jml_racepack_n = $sql5->fetch_assoc();
        $sql6 = $this->db->query("SELECT count(*) as jml_racepack_y  FROM tb_pendaftaran WHERE status_racepack = 'Y'");
        $jml_racepack_y = $sql6->fetch_assoc();
        
        
        return $response->withJson(["status" => "success", "jml1" => $jml_user, "jml2" => $jml_peserta, "jml3" => $jml_belumbayar, "jml4" => $jml_lunas, "jml5" => $jml_racepack_n, "jml6" => $jml_racepack_y ], 200);
        
    });

    $app->post('/update_pembayaran/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $id_user = $jsonParams['id_user'];
		$status_bayar = $jsonParams['status_bayar'];

        $query = "UPDATE tb_pendaftaran SET status_bayar = '$status_bayar' WHERE id_user = '$id_user'";

        if ($this->db->query($query)) {
            return $response->withJson(["status" => "success", "message" => "Register success!"], 200);
        } else {
            return $response->withJson(["status" => "failed", "message" => "Register failed!"], 404);
        }
    });

    $app->post('/update_racepack/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $id_user = $jsonParams['id_user'];
        $status_racepack = $jsonParams['status_racepack'];

        $query = "UPDATE tb_pendaftaran SET status_racepack = '$status_racepack' WHERE id_user = '$id_user'";

        if ($this->db->query($query)) {
            return $response->withJson(["status" => "success", "message" => "Register success!"], 200);
        } else {
            return $response->withJson(["status" => "failed", "message" => "Register failed!"], 404);
        }
    });
	
	$app->get('/data_peserta/', function (Request $request, Response $response) {
        $sql = "SELECT * FROM tb_pendaftaran p join tb_user u on p.id_user = u.id_user";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch_all(MYSQLI_ASSOC);

        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    //1. Get All User
    $app->get('/users_blmbayar/', function (Request $request, Response $response) {
        $sql = "SELECT * FROM tb_pendaftaran p join tb_user u on p.id_user = u.id_user where p.status_bayar='pending'";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch_all(MYSQLI_ASSOC);

        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->get('/users_sdhbayar/', function (Request $request, Response $response) {
        $sql = "SELECT * FROM tb_pendaftaran p join tb_user u on p.id_user = u.id_user where p.status_bayar='lunas'";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch_all(MYSQLI_ASSOC);

        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->get('/users_racepack_n/', function (Request $request, Response $response) {
        $sql = "SELECT * FROM tb_pendaftaran p join tb_user u on p.id_user = u.id_user where p.status_racepack='N'";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch_all(MYSQLI_ASSOC);

        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->get('/users_racepack_y/', function (Request $request, Response $response) {
        $sql = "SELECT * FROM tb_pendaftaran p join tb_user u on p.id_user = u.id_user where p.status_racepack='Y'";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch_all(MYSQLI_ASSOC);

        return $response->withJson(["status" => "success", "data" => $result], 200);
    });

    $app->post('/resetpassword', function (Request $request, Response $response){
        $mail = new PHPMailer(true);
        $jsonParams = $request->getParsedBody();
        $email_user = $jsonParams['email_user'];

        $sqlCheckUsernameAndEmail = $this->db->query("SELECT * FROM tb_user WHERE email_user = '$email_user'");
        $rowCheckUsernameAndEmail = $sqlCheckUsernameAndEmail->fetch_all(MYSQLI_ASSOC);
        $row = $sqlCheckUsernameAndEmail->num_rows;
        if($row == 1)
        {
            try
            {
                $mail->isSMTP();
                $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 587;
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->SMTPAuth = true;
                $mail->Username = 'pefindotest@gmail.com';
                $mail->Password = 'pefindotest123';
                $mail->setFrom("admin@otten32run.com", "No Reply");
                $mail->addAddress($email_user, $rowCheckUsernameAndEmail[0]['nama_lengkap']);
                //Mail Content
                $mail->isHTML(true);
                $mail->Subject = "Lupa Password akun Otten32run";
                $mail->Body = "Password anda adalah <b>".$rowCheckUsernameAndEmail[0]['password_user']."</b>";
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                if($mail->send())
                {
                    return $response->withJson(["status" => "success", "message" => "Password terkirim ke ".$email_user."!"])->withStatus(200)->withHeader('Content-Type', 'application/json');
                }
                else
                {
                    return $response->withJson(["status" => "error", "message" => $mail->ErrorInfo])->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }
            catch (Exception $e)
            {
                return $response->withJson(["status" => "error", "message" => $mail->ErrorInfo])->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        }
        else
        {
            return $response->withJson(["status" => "error", "message" => "Email tidak terdaftar!"])->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });


};
