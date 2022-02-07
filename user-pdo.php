<?php


class Userpdo
{
    //Propriétés
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $pdo;

    // Connexion à la bdd avec PDO

    public function __construct()
    {
        // on appel notre méthode PDO
        $this->pdo = $this->getPDO();
    }

    public function register($login, $password, $email, $firstname, $lastname)
    {
        // On prépare la requete et l'execute.
        $stmt = $this->getPDO()->prepare("INSERT INTO utilisateurs (login, email, password, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$login, $email, $password, $firstname, $lastname]);

        // retourne un tableau de valeurs
        // (int) pour définir le type de la valeur de retour
        // LastInsertId = on récupère la dernière ligne
        return [
            'id' => (int)$this->getPDO()->lastInsertId(),
            'login' => $login,
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname
        ];
    }

    //Méthode de connexion
    public function connect($login, $password)
    {
        $stmt = $this->getPDO()->prepare("SELECT * FROM utilisateurs WHERE login = ? AND password = ?");
        $stmt->execute([$login, $password]);
        // On veut recuperer un tableau de notre utilisateur grace au statment
        $userArray = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userArray) {
            foreach ($userArray as $key => $value) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    public function isConnected()
    {
        return $this->id !== null;
    }

    public function disconnect()
    {
        if ($this->isConnected()) {
            foreach ($this as $key => $value) {
                $this->$key = null;
            }
        }
    }

    public function delete()
    {
        if ($this->isConnected()) {
            $stmt = $this->getPDO()->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$this->id]);
            $this->disconnect();
        }
    }

    //Méthode pour mettre à jour les attributs de l'objet et modifie les infos en BDD
    public function update($login, $password, $email, $firstname, $lastname)
    {
        $stmt = $this->getPDO()->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
        $result = $stmt->execute([$login, $password, $email, $firstname, $lastname, $this->id]);
        if ($result) {
            return $this->connect($login, $password);
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
    public function getLogin()
    {
        return $this->login;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }


    public function getPDO()
    {
        // on vérifie si la connexion à la bdd se existe, sinon on en creer une
        if ($this->pdo === null) {
            $this->pdo = new PDO("mysql:host=localhost;dbname=classes", 'root', '');
        }
        return $this->pdo;
    }
}
// On creer une instance de notre user et on appel la méthode..
$user = new Userpdo();
// $res = $user->register('login2', 'password', 'email', 'firstname', 'lastname');
// var_dump($res);
$user->connect('login1', 'password');
$user->update('login2', 'password2', 'email', 'firstname', 'lastname');

// $user->delete();
var_dump($user);
