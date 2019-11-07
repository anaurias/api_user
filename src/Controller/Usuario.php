<?php

include_once './database/Conexao.php';

class Usuario
{

    private $conexaoBd;

    public function __construct()
    {
        $this->conexaoBd = Conexao::getInstance();
    }

    public function buscaUsuario()
    {

        $sth = $this->conexaoBd->prepare("select name, email from users");

        $sth->execute();

        $result = $sth->fetchAll(PDO::FETCH_OBJ);

        echo json_encode($result);
    }

    public function incluirUsuario($jsonBody)
    {
        try {
            $this->conexaoBd->beginTransaction();

            if (!empty($this->validaEmail($jsonBody['email']))) {

                $arr = array('Message' => "Email Cadastrado Por favor Verifique");
                return json_encode($arr);
            }

            $stmt = $this->conexaoBd->prepare("insert into users(name, email, password_hash, provider, created_at, updated_at) VALUES (:name, :email, :password_hash, :provider, current_date, current_date)");
            $stmt->bindValue('name', $jsonBody['name'], PDO::PARAM_STR);
            $stmt->bindValue('email', $jsonBody['email'], PDO::PARAM_STR);
            $stmt->bindValue('password_hash', $jsonBody['password_hash'], PDO::PARAM_STR);
            $stmt->bindValue('provider', $jsonBody['provider'], PDO::PARAM_BOOL);

            $stmt->execute();

            $this->conexaoBd->commit();

            return json_encode(array('Message' => 'Dados Inseridos com sucesso'));

        } catch (PDOException $e) {

            echo "Ocorreu um erro ao tentar inserir os dados" . $e;

            $this->conexaoBd->rollback();
        }
    }

    public function updateUsuario($jsonBody)
    {
        try {
            $this->conexaoBd->beginTransaction();

            if (empty($this->validaEmail($jsonBody['email']))) {

                $arr = array('Message' => "Não foi possível alterar os dados o email: " . $jsonBody['email'] . " não exite");

                echo json_encode($arr);

                exit();
            }

            $data = [
                'name' => $jsonBody['name'],
                'email' => $jsonBody['email'],
                'password_hash' => $jsonBody['password_hash'],
                'provider' => $jsonBody['provider'],
            ];
            $sql = "UPDATE users SET name=:name, password_hash=:password_hash, provider=:provider WHERE email=:email";
            $stmt = $this->conexaoBd->prepare($sql);
            $stmt->execute($data);

            $this->conexaoBd->commit();

            echo json_encode(array('Message' => 'Dados Alterados com sucesso'), JSON_NUMERIC_CHECK);

        } catch (PDOException $e) {
            echo "Ocorreu um erro " . $e;
        }

    }

    public function deleteUsuario($email)
    {

        try {
            if (empty($this->validaEmail($email))) {

                $arr = array('Message' => "Email não encontrado. Por favor verificar: " . $email . " não exite");

                echo json_encode($arr);

                exit();
            }
            $data = [
                'email' => $email,
            ];

            $sql = "delete from users WHERE email=:email";
            $stmt = $this->conexaoBd->prepare($sql);
            $stmt->execute($data);

            echo json_encode(array('Message' => 'Usuário deletado com Sucesso'), JSON_NUMERIC_CHECK);

        } catch (PDOException $e) {
            echo "Ocorreu um erro ao tentar deletar usuario " . $e;
            $this->conexaoBd->rollback();
        }
    }

    private function validaEmail($email)
    {
        $stmt = $this->conexaoBd->prepare("SELECT * FROM users WHERE email=:email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        return $user->email;
    }

    public function validaEmailPreenchido()
    {
        if (!array_key_exists('email', $_REQUEST)) {
            return json_encode(array('Message' => 'email não informado por favor verificar', 'status' => 'false'));
        }
    }
}
