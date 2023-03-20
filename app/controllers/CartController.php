<?php

class CartController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = $this->model('Cart');
    }

    public function index($errors = [])
    {
        $session = new Session();

        if ($session->getLogin()) {

            $user_id = $session->getUserId();
            $cart = $this->model->getCart($user_id);

            $data = [
                'titulo' => 'Carrito',
                'menu' => true,
                'user_id' => $user_id,
                'data' => $cart,
                'errors' => $errors
            ];

            $this->view('carts/index', $data);

        } else {
            header('location:' . ROOT);
        }
    }

    public function addProduct($product_id, $user_id)
    {
        $errors = [];

        if ($this->model->verifyProduct($product_id, $user_id) == false) {
            if ($this->model->addProduct($product_id, $user_id) == false) {
                array_push($errors, 'Error al insertar el producto en el carrito');
            }
        }
        $this->index($errors);
    }

    public function update()
    {
        if (isset($_POST['rows']) && isset($_POST['user_id'])) {
            $errors = [];
            $rows = $_POST['rows'];
            $user_id = $_POST['user_id'];

            for ($i = 0; $i < $rows; $i++) {
                $product_id = $_POST['i'.$i];
                $quantity = $_POST['c'.$i];
                if ( ! $this->model->update($user_id, $product_id, $quantity)) {
                    array_push($errors, 'Error al actualizar el producto');
                }
            }
            $this->index($errors);
        }
    }

    public function delete($product, $user)
    {
        $errors = [];

        if( ! $this->model->delete($product, $user)) {
            array_push($errors, 'Error al borrar el registro del carrito');
        }

        $this->index($errors);
    }

    public function checkout()
    {
        $session = new Session();

        if ($session->getLogin()) {

            $user = $session->getUser();

            $data = [
                'titulo' => 'Carrito | Datos de envío',
                'subtitle' => 'Checkout | Verificar dirección de envío',
                'menu' => true,
                'data' => $user,
            ];
            //$this->view('carts/address', $data);
            $this->view('carts/select_address', $data);

        } else {
            $data = [
                'titulo' => 'Carrito | Checkout',
                'subtitle' => 'Checkout | Iniciar sesion',
                'menu' => true
            ];

            $this->view('carts/checkout', $data);
        }
    }

    public function address()
    {
        $session = new Session();

        if ($session->getLogin())
        {
            $user = $session->getUser();

            $data = [
                'titulo' => 'Carrito | Dirección',
                'subtitle' => 'Checkout | Modificar dirección',
                'menu' => true,
                'data' => $user,
            ];

            $this->view('carts/address', $data);
        }
        else
        {
            $data = [
                'titulo' => 'Carrito | Checkout',
                'subtitle' => 'Checkout | Iniciar sesion',
                'menu' => true
            ];

            $this->view('carts/checkout', $data);
        }


    }

    public function paymentmode()
    {
        $data = [
            'titulo' => 'Carrito | Forma de pago',
            'subtitle' => 'Checkout | Forma de pago',
            'menu' => true,
        ];

        $this->view('carts/paymentmode', $data);
    }

    public function validate_form()
    {
        // GUARDAR DATOS POST (si viene por post, hago X. Si no hago Y)
        // POST > validar datos y guardar en la bbdd "address"

        //Esto es lo último que habrá que poner en el "si viene por POST"


        $session = new Session();

        if(!$session->getUser()){
            header('location:'.ROOT);
        }

        $errors = [];
        $user = $session->getUser();
        $userData = (array) $user; // almacena los posibles datos distintos a los de sesión y una copia de los no modificados
        $addressDetails = [];

        // se viene de la vista address // sólo se accede por POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Validar datos del formulario de la vista address
            $firstName = Validate::text($_POST['first_name'] ?? '');
            $lastName1 = Validate::text($_POST['last_name_1'] ?? '');
            $lastName2 = Validate::text($_POST['last_name_2'] ?? '');
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL); // elimina caracteres no válidos para un mail
            $address = Validate::text($_POST['address'] ?? '');
            $city = Validate::text($_POST['city'] ?? '');
            $state = Validate::text($_POST['state'] ?? '');
            $zipcode = Validate::text($_POST['zipcode'] ?? '');
            $country = Validate::text($_POST['country'] ?? '');


            // Comparamos los valores del user de sesión ya validados...
            if ($user->first_name != $firstName){
                if (empty($firstName) || !is_string($firstName)){
                    $errors[] = 'El nombre no cumple con el formato';
                    $userData['first_name'] = $user->first_name; // memoria para el formulario con data base
                }
                else{
                    $userData['first_name'] = $firstName;
                }
            }

            if ($user->last_name_1 != $lastName1){
                if (empty($lastName1) || !is_string($lastName1)){
                    $errors[] = 'El primer apellido no cumple con el formato';
                    $userData['last_name_1'] = $user->last_name_1; // memoria para el formulario con data base
                }
                else{
                    $userData['last_name_1'] = $lastName1;
                }
            }

            if ($user->last_name_2 != $lastName2){
                if (empty($lastName2) || !is_string($lastName2)){
                    $errors[] = 'El segundo apellido no cumple con el formato';
                    $userData['last_name_2'] = $user->last_name_2; // memoria para el formulario con data base
                }
                else{
                    $userData['last_name_2'] = $lastName2;
                }
            }

            if ($user->email != $email){
                if (empty($email) || !is_string($email)){
                    $errors[] = 'El email no cumple con el formato';
                    $userData['email'] = $user->email; // memoria para el formulario con data base
                }
                else{
                    $userData['email'] = $email;
                }
            }

            if ($user->address != $address){
                if (empty($address) || !is_string($address)){
                    $errors[] = 'La dirección no cumple con el formato';
                    $userData['address'] = $user->address; // memoria para el formulario con data base
                }
                else{
                    $userData['address'] = $address;
                }
            }

            if ($user->city != $city){
                if (empty($city) || !is_string($city)){
                    $errors[] = 'La ciudad no cumple con el formato';
                    $userData['city'] = $user->city; // memoria para el formulario con data base
                }
                else{
                    $userData['city'] = $city;
                }
            }

            if ($user->state != $state){
                if (empty($state) || !is_string($state)){
                    $errors[] = 'La ciudad no cumple con el formato';
                    $userData['state'] = $user->state; // memoria para el formulario con data base
                }
                else{
                    $userData['state'] = $state;
                }
            }

            if ($user->zipcode != $zipcode){
                if (empty($zipcode) || !is_string($zipcode)){
                    $errors[] = 'La ciudad no cumple con el formato';
                    $userData['zipcode'] = $user->zipcode; // memoria para el formulario con data base
                }
                else{
                    $userData['zipcode'] = $zipcode;
                }
            }

            if ($user->country != $country){
                if (empty($country) || !is_string($country)){
                    $errors[] = 'La ciudad no cumple con el formato';
                    $userData['country'] = $user->country; // memoria para el formulario con data base
                }
                else{
                    $userData['country'] = $country;
                }
            }

            // Array de nuevos datos para la inserción de la dirección en el carrito
            $addressDetails = [
                'name' => $userData['first_name'],
                'last_name_1' => $userData['last_name_1'],
                'last_name_2' => $userData['last_name_2'],
                'email' => $userData['email'],
                'address' => $userData['address'],
                'city' => $userData['city'],
                'state' => $userData['state'],
                'zipcode' => $userData['zipcode'],
                'country' => $userData['country'],
            ];

        }
        else{
            // se viene por GET, por lo que no se ha modificado el formulario y los capos son los mismos que la sesión actual
            $addressDetails = [
                'name' => $user['first_name'],
                'last_name_1' => $user['last_name_1'],
                'last_name_2' => $user['last_name_2'],
                'email' => $user['email'],
                'address' => $user['address'],
                'city' => $user['city'],
                'province' => $user['state'],
                'zipcode' => $user['zipcode'],
                'country' => $user['country'],
            ];
        }

        if (!count($errors)){
            $data = [
                'titulo' => 'Carrito | Forma de pago',
                'subtitle' => 'Checkout | Forma de pago',
                'menu' => true,
                'userData' => $addressDetails,
            ];
            $_SESSION['newAddress'] = $data['userData'];

            $this->model->addAddress($user->id, $addressDetails); // insertar datos enviados a base de datos
            $this->view('carts/paymentmode', $data);
        }
        else{
            $data = [
                'titulo' => 'Datos de envío',
                'subtitle' => 'Por favor, compruebe los datos de envío y cambie lo que considere necesario',
                'menu' => true,
                'userData' => $userData,
                'errors' => $errors,
            ];

            // Redirección vista con errores
            $this->view('carts/address', $data);
        }
    }

    public function verify()
    {
        $session = new Session();
        $user = $session->getUser();
        $cart = $this->model->getCart($user->id);
        $payment = $_POST['payment'] ?? '';

        $data = [
            'titulo' => 'Carrito | Verificar los datos',
            'menu' => true,
            'payment' => $payment,
            'user' => $user,
            'data' => $cart,
            'newAddress' => $_SESSION['newAddress'],
        ];

        $this->view('carts/verify', $data);
    }

    public function thanks()
    {
        $session = new Session();
        $user = $session->getUser();

        if ($this->model->closeCart($user->id, 1)) {

            $data = [
                'titulo' => 'Carrito | Gracias por su compra',
                'data' => $user,
                'menu' => true,
            ];

            $this->view('carts/thanks', $data);

        } else {

            $data = [
                'titulo' => 'Error en la actualización del carrito',
                'menu' => false,
                'subtitle' => 'Error en la actualización de los productos del carrito',
                'text' => 'Existió un problema al actualizar el estado del carrito. Por favor, pruebe más tarde o comuníquese con nuestro servicio de soporte',
                'color' => 'alert-danger',
                'url' => 'login',
                'colorButton' => 'btn-danger',
                'textButton' => 'Regresar',
            ];

            $this->view('mensaje', $data);

        }


    }
}