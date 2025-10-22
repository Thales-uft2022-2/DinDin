
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Inclui o AuthService
require_once __DIR__ . '/../services/AuthService.php';

class AuthController {
    // Removido $userModel, pois todas as ações agora usam AuthService
    private $authService; // Dependência do serviço

    public function __construct() {
        // Instancia o serviço
        $this->authService = new AuthService();
        // Não precisa mais instanciar UserModel aqui
    }

    // =======================================================
    // MÉTODO 'register' (WEB) - REFATORADO (TS-Auth-01)
    // =======================================================
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $result = $this->authService->registerUser($data);
            if ($result['success']) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['success_message'] = $result['message'];
                header("Location: " . BASE_URL . "/auth/login");
                exit;
            } else {
                $errors = $result['errors'];
                $oldData = $data;
                include __DIR__ . '/../views/auth/register.php';
            }
        } else {
            if (session_status() === PHP_SESSION_NONE) session_start();
            unset($_SESSION['success_message']);
            include __DIR__ . '/../views/auth/register.php';
        }
    }

    // =======================================================
    // MÉTODO 'apiRegister' (API) - NOVO (TS-Auth-01)
    // =======================================================
    public function apiRegister() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }
        $result = $this->authService->registerUser($data);
        if ($result['success']) {
            http_response_code(201);
            echo json_encode(['message' => $result['message'], 'user' => $result['user']]);
        } else {
            http_response_code(422);
            echo json_encode(['errors' => $result['errors']]);
        }
    }

    // =======================================================
    // MÉTODO 'login' (WEB) - REFATORADO (TS-Auth-02)
    // =======================================================
    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Se já estiver logado (sessão ativa), redireciona para home
        if (!empty($_SESSION['user'])) {
            header("Location: " . BASE_URL . "/home");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $credentials = $_POST;
            $result = $this->authService->loginUser($credentials);
            if ($result['success']) {
                header("Location: " . BASE_URL . "/home");
                exit;
            } else {
                $error = $result['errors'][0] ?? 'Erro desconhecido no login.';
                $success_message = $_SESSION['success_message'] ?? null;
                unset($_SESSION['success_message']);
                include __DIR__ . '/../views/auth/login.php';
            }
        } else {
            $success_message = $_SESSION['success_message'] ?? null;
            unset($_SESSION['success_message']);
            include __DIR__ . '/../views/auth/login.php';
        }
    }

    // =======================================================
    // MÉTODO 'apiLogin' (API) - NOVO (TS-Auth-02)
    // =======================================================
    public function apiLogin() {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }
        $credentials = json_decode(file_get_contents('php://input'), true);
        if (!is_array($credentials)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }
        $result = $this->authService->loginUser($credentials);
        if ($result['success']) {
            http_response_code(200);
            echo json_encode(['message' => $result['message'], 'user' => $result['user'], 'session_id' => session_id()]);
        } else {
            http_response_code(401);
            echo json_encode(['errors' => $result['errors']]);
        }
    }

    // =======================================================
    // MÉTODO 'logout' (WEB) - REFATORADO (TS-Auth-03)
    // =======================================================
    public function logout() {
        $this->authService->logoutUser();
        header("Location: " . BASE_URL . "/auth/login");
        exit;
    }

    // =======================================================
    // MÉTODO 'apiLogout' (API) - NOVO (TS-Auth-03)
    // =======================================================
    public function apiLogout() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             http_response_code(405);
             echo json_encode(['error' => 'Método não permitido, use POST']);
             return;
        }
        $result = $this->authService->logoutUser();
        http_response_code(200);
        echo json_encode(['message' => $result['message']]);
    }

    // =======================================================
    // MÉTODOS DE RECUPERAÇÃO DE SENHA (WEB) - REFATORADOS (TS-Auth-04)
    // =======================================================

    /**
     * Exibe o formulário para solicitar a redefinição (WEB).
     */
    public function forgotPassword() {
        // Apenas exibe a view
        include __DIR__ . '/../views/auth/forgot-password.php';
    }

    /**
     * Processa a solicitação de redefinição de senha (WEB).
     * Chama o AuthService para lidar com a lógica e o envio do e-mail.
     */
    public function sendResetLink() {
        $message = ''; // Mensagem a ser exibida na view
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            // Chama o serviço, que sempre retorna a mensagem genérica por segurança
            $result = $this->authService->requestPasswordReset($email);
            $message = $result['message']; // Pega a mensagem do serviço
        }
        // Exibe a view que mostra a mensagem (seja do POST ou não)
        include __DIR__ . '/../views/auth/reset-link-sent.php';
    }

    /**
     * Exibe o formulário para criar uma nova senha (link do e-mail - WEB).
     * Verifica se o token é válido antes de mostrar o formulário.
     */
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        $user = $this->authService->verifyResetToken($token); // Usa o serviço para verificar

        if (!$user) {
            // Se o token for inválido ou expirado, mostra uma mensagem de erro
            // Poderia ser uma view de erro mais elaborada
            include_once __DIR__ . '/../views/_header.php'; // Usa header/footer
            echo '<div class="form-container"><h1>Erro</h1><p>Token de redefinição inválido ou expirado. Por favor, solicite um novo link.</p><p><a href="' . BASE_URL . '/auth/forgot-password">Solicitar Novo Link</a></p></div>';
            include_once __DIR__ . '/../views/_footer.php';
            exit;
        }

        // Se o token for válido, passa o token para a view
        include __DIR__ . '/../views/auth/reset-password.php';
    }

    /**
     * Processa o formulário de nova senha (WEB).
     * Chama o AuthService para validar token e atualizar a senha.
     */
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            // Chama o serviço para redefinir a senha
            $result = $this->authService->resetPasswordWithToken($token, $password, $password_confirm);

            if ($result['success']) {
                // Sucesso: mostra a página de sucesso
                include __DIR__ . '/../views/auth/password-reset-success.php';
            } else {
                // Erro: Recarrega o formulário mostrando os erros
                $errors = $result['errors'];
                // Precisamos passar o token de volta para a view
                include __DIR__ . '/../views/auth/reset-password.php';
            }
        } else {
             // Se não for POST, redireciona para login (ou outra página)
             header("Location: " . BASE_URL . "/auth/login");
             exit;
        }
    }

    // =======================================================
    // MÉTODOS DE RECUPERAÇÃO DE SENHA (API) - NOVOS (TS-Auth-04)
    // =======================================================

    /**
     * Solicita o envio do link de redefinição de senha via API.
     */
    public function apiForgotPassword() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido ou campo "email" ausente.']);
            return;
        }

        // Chama o serviço (que sempre retorna a mesma mensagem por segurança)
        $result = $this->authService->requestPasswordReset($data['email']);

        // Retorna a mensagem genérica
        http_response_code(200); // OK (mesmo se o e-mail não existir)
        echo json_encode(['message' => $result['message']]);
    }

    /**
     * Redefine a senha via API usando o token.
     */
    public function apiResetPassword() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido, use POST']);
            return;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido.']);
            return;
        }

        // Pega os dados necessários do JSON
        $token = $data['token'] ?? '';
        $password = $data['password'] ?? '';
        $password_confirm = $data['password_confirm'] ?? '';

        // Chama o serviço para redefinir a senha
        $result = $this->authService->resetPasswordWithToken($token, $password, $password_confirm);

        // Retorna a resposta
        if ($result['success']) {
            http_response_code(200); // OK
            echo json_encode(['message' => $result['message']]);
        } else {
            // Pode ser 422 (validação) ou 400/404 (token inválido)
            // Vamos usar 400 como genérico para token/validação aqui
            http_response_code(400);
            echo json_encode(['errors' => $result['errors']]);
        }
    }


    // --- FUNÇÃO AUXILIAR DE EMAIL ---
    // Mantida aqui por enquanto, mas idealmente iria para uma classe separada (MailerService, NotificationService)
    // O AuthService agora chama a interna: sendPasswordResetEmailInternal
    private function sendPasswordResetEmail($email, $token) {
       // Este método não é mais chamado diretamente,
       // mas pode ser mantido se outra parte do código o usar.
       // A lógica foi movida para AuthService->sendPasswordResetEmailInternal
       trigger_error("AuthController::sendPasswordResetEmail is deprecated. Use AuthService.", E_USER_DEPRECATED);
       // Poderia chamar o método do serviço se necessário manter compatibilidade
       // $this->authService->sendPasswordResetEmailInternal($email, $token); // Não recomendado misturar assim
    }

}
