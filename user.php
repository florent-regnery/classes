<?php
echo '<pre>';

class User
{
    // l'attribut il accessible uniquement dans la classe
    // private $id;
    // l'attribut il accessible partout
    // public $price = 25;
    // l'attribut il accessible partout dans les enfants et reste privé à l'exterieur (héritage)
    // protected

    private $id;

    public $login;

    public $email;

    public $firstname;

    public $lastname;

    private $mysqli;

    // function qui est appelé au moment de la création d'une instance de notre classe
    public function __construct()
    {
        $this->mysqli = new mysqli("localhost", "root", "", "classes");
    }
    public function register($login, $password, $email, $firstname, $lastname)
    {

        if ($this->mysqli->connect_errno) {
            echo "Échec lors de la connexion à MySQL : (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
        }
        $stmt = $this->mysqli->prepare("INSERT INTO utilisateurs(login, password, email, firstname, lastname) VALUES (?,?,?,?,?)");
        if ($stmt) {
            if (!$stmt->bind_param('sssss', $login, $password, $email, $firstname, $lastname)) {
                echo "Échec lors du liage des paramètres : (" . $stmt->errno . ") " . $stmt->error;
            }
            if (!$stmt->execute()) {
                if ($stmt->errno === 1062) {
                    echo 'Ce compte existe déjà, veuillez utiliser un autre identifiant';
                } else {
                    echo "Échec lors de l'exécution de la requête : (" . $stmt->errno . ") " . $stmt->error;
                }
            } else {
                return [
                    'id' => $this->mysqli->insert_id,
                    'login' => $login,
                    'email' => $email,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                ];
            }
            return [];
        } else {
            echo 'Une erreur est survenue';
        }
    }


    public static function connectStatic($login, $password)
    {
        $mysqli = new mysqli("localhost", "root", "", "classes");
        $stmt = $mysqli->prepare("
            SELECT id, login, email, firstname, lastname 
            FROM utilisateurs 
            WHERE login = ? AND password = ? 
        ");
        $stmt->bind_param('ss', $login, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_object(User::class);
    }

    public function connect($login, $password)
    {
        $stmt = $this->mysqli->prepare("
            SELECT id, login, email, firstname, lastname 
            FROM utilisateurs
            WHERE login = ? AND password = ? 
        ");
        $stmt->bind_param('ss', $login, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $userArray = $result->fetch_assoc();
        $this->id = $userArray['id'];
        $this->login = $userArray['login'];
        $this->email = $userArray['email'];
        $this->firstname = $userArray['firstname'];
        $this->lastname = $userArray['lastname'];
        return $this;
    }

    public function disconnect()
    {
        if ($this->isConnected()) {
            foreach ($this as $key => $value) {
                if ($key !== 'mysqli') {
                    $this->$key = null;
                }
            }
        }
    }

    public function delete()
    {
        if ($this->isConnected()) {
            $stmt = $this->mysqli->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->bind_param('i', $this->id);
            $stmt->execute();
            $this->disconnect();
        }
    }

    public function update($login, $password, $email, $firstname, $lastname)
    {
        $stmt = $this->mysqli->prepare("
            UPDATE utilisateurs 
            SET login = ?,
            password = ?,
            email = ?,
            firstname = ?,
            lastname = ?
            WHERE id = ?
        ");
        $stmt->bind_param('sssssi', $login, $password, $email, $firstname, $lastname, $this->id);
        if ($stmt->execute()) {
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
        }
    }

    public function getAllInfos()
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname
        ];
    }

    public function isConnected()
    {
        return $this->id !== null;
    }


    /**
     * Get the value of login
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the value of firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Get the valueof lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    public function getMysqli()
    {
        if ($this->mysqli === null) {
            $this->mysqli = new mysqli("localhost", "root", "", "classes");
        }
        return $this->mysqli;
    }
}

$user = new User();
$user2 = new User();

// var_dump($user, $user2);
// die;
// $user->register('Flo1', 'motdepasse', 'florent.r@gmail.com', 'Florent', 'Regnery');
$user->connect('Flo1', 'motdepasse');
var_dump($user->getLogin(), $user2->isConnected());
die;

$user->update('Flo5555', 'motdepasse', 'florent.r@gmail.com', 'Florent', 'Regnery');
var_dump($user);
//$user->connect('Flo1', 'motdepasse')
