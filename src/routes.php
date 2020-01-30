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
            $sql = $this->db->query("DELETE FROM tb_pendaftaran WHERE id_user = '$id_user'");
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
		
		$query = "SELECT * FROM tb_user WHERE email_user='$email_user'";
        $res = $this->db->query($query);
        $row = $res->num_rows;
        if ($row == 0) {
			
			$query = "INSERT INTO tb_user(nama_lengkap, email_user, password_user, nomor_hp, level_akses)
				VALUES ('$nama_lengkap', '$email_user', '$password_user', '$nomor_hp', '2')";

			if ($this->db->query($query)) {
				return $response->withJson(["status" => "success", "message" => "Register success!"], 200);
			} else {
				return $response->withJson(["status" => "failed", "message" => "Register failed!"], 404);
			}
			
		} else {
            return $response->withJson(["status" => "failed", "message" => "Email sudah terdaftar!"], 404);
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
            $institusi = $jsonParams['institusi'];
            $pekerjaan = $jsonParams['pekerjaan'];
            $total_bayar = 225000+(intval($id_user));

            $query = "INSERT INTO tb_pendaftaran(id_user, nomor_identitas, jenis_kelamin, alamat_user, tempat_lahir, tanggal_lahir, golongan_darah, riwayat_kesehatan, riwayat_kesehatan_keluarga, obat_pribadi, size_chart, total_bayar, status_bayar, status_racepack, tgl_pendaftaran, institusi, pekerjaan)
            VALUES ('$id_user', '$nomor_identitas', '$jenis_kelamin', '$alamat_user', '$tempat_lahir', '$tanggal_lahir', '$golongan_darah', '$riwayat_kesehatan', '$riwayat_kesehatan_keluarga', '$obat_pribadi', '$size_chart', '$total_bayar', 'pending', 'N', '$tgl_pendaftaran','$institusi','$pekerjaan')";

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
        $sql5 = $this->db->query("SELECT count(*) as jml_racepack_n  FROM tb_pendaftaran WHERE status_racepack = 'N' AND status_bayar = 'lunas'");
        $jml_racepack_n = $sql5->fetch_assoc();
        $sql6 = $this->db->query("SELECT count(*) as jml_racepack_y  FROM tb_pendaftaran WHERE status_racepack = 'Y' AND status_bayar = 'lunas'");
        $jml_racepack_y = $sql6->fetch_assoc();
		$sql7 = $this->db->query("SELECT count(*) as jml_daftar FROM tb_pendaftaran WHERE tgl_pendaftaran = CURDATE()");
        $jml_daftar = $sql7 ->fetch_assoc();
        
        
        return $response->withJson(["status" => "success", "jml1" => $jml_user, "jml2" => $jml_peserta, "jml3" => $jml_belumbayar, "jml4" => $jml_lunas, "jml5" => $jml_racepack_n, "jml6" => $jml_racepack_y, "jml7" => $jml_daftar ], 200);
        
    });

    $app->post('/update_pembayaran/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $id_user = $jsonParams['id_user'];
		$status_bayar = $jsonParams['status_bayar'];

        if ($status_bayar == 'lunas') {
            $query = $this->db->query("UPDATE tb_pendaftaran SET status_bayar = '$status_bayar' WHERE id_user = '$id_user'");
            $sqlCheck = $this->db->query("SELECT * FROM tb_user WHERE id_user = '$id_user'");
            $data = $sqlCheck->fetch_all(MYSQLI_ASSOC);
             try {
                $mail = new PHPMailer(true);  
                $mail->isSMTP();
                $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 587;
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->SMTPAuth = true;
                $mail->Username = 'otten32run@gmail.com';
                $mail->Password = 'otten32run123';
                $mail->setFrom("otten32run@gmail.com", "Pembayaran Otten32run Terkonfirmasi");
                $mail->addAddress($data[0]['email_user'], $data[0]['nama_lengkap']);
                //Mail Content
                $mail->isHTML(true);
                $mail->Subject = "Download tiket";
                $mail->Body = 'Terimakasih sudah melakukan pembayaran, <br><br> Silahkan download tiket anda <a href=http://35.187.253.244:8088/cetaktiket/?idUser='.$data[0]['id_user'].'>disini</a>';
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                if($mail->send())
                {
                    return $response->withJson(["status" => "success", "message" => "Sudah Bayar!"])->withStatus(200)->withHeader('Content-Type', 'application/json');
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

        } elseif ($status_bayar == 'pending') {
            $query = $this->db->query("UPDATE tb_pendaftaran SET status_bayar = '$status_bayar' WHERE id_user = '$id_user'");
            return $response->withJson(["status" => "success", "message" => "Cancel bayar!"], 200);
        } else {
            return $response->withJson(["status" => "failed", "message" => "failed!"], 404);
        }
    });

    $app->post('/voucher/', function (Request $request, Response $response) {
        $jsonParams = $request->getParsedBody();
        $voucher = $jsonParams['voucher'];
        $id_user = $jsonParams['id_user'];
        $v175 = 175000+(intval($id_user));
        $v200 = 200000+(intval($id_user));
        
        $query = $this->db->query("SELECT * FROM voucher WHERE kode_voucher = '$voucher' and is_used = 'N'");
        $row = $query->num_rows;
        $data = $query->fetch_all(MYSQLI_ASSOC);

        if($row == 1) {
            if ($data[0]['kategori'] == 'M') {
                $query2 = $this->db->query("UPDATE tb_pendaftaran SET total_bayar = '$v175' WHERE id_user = '$id_user'");
                $query2 = $this->db->query("UPDATE voucher SET is_used = 'Y' WHERE kode_voucher = '$voucher'");
                return $response->withJson(["status" => "success", "message" => "Voucher Mahasiswa (175k) terpasang!"], 200);
            } elseif ($data[0]['kategori'] == 'K') {
                $query2 = $this->db->query("UPDATE tb_pendaftaran SET total_bayar = '$v200' WHERE id_user = '$id_user'");
                $query2 = $this->db->query("UPDATE voucher SET is_used = 'Y' WHERE kode_voucher = '$voucher'");
                return $response->withJson(["status" => "success", "message" => "Voucher Komunitas (200k) terpasang!"], 200);
            } elseif ($data[0]['kategori'] == 'A') {
                $query2 = $this->db->query("UPDATE tb_pendaftaran SET total_bayar = '$v200' WHERE id_user = '$id_user'");
                $query2 = $this->db->query("UPDATE voucher SET is_used = 'Y' WHERE kode_voucher = '$voucher'");
                return $response->withJson(["status" => "success", "message" => "Voucher Alumni (200k) terpasang!"], 200);
            }
        } else {
            return $response->withJson(["status" => "failed", "message" => "Voucher tidak ditemukan!"], 404);
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
                $mail->Username = 'otten32run@gmail.com';
                $mail->Password = 'otten32run123';
                $mail->setFrom("otten32run@gmail.com", "Lupa password");
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
