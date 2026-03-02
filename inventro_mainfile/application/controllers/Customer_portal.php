<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Portal do Cliente
 *
 * Controller publico (extends CI_Controller, sem RBAC).
 * Autenticacao propria via sessao: cliente_logado, cliente_id, cliente_nome, cliente_email.
 *
 * Padrao identico ao Motoboy.php.
 */
class Customer_portal extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Customer_portal_model');
        $this->load->helper(['url', 'html']);
    }

    // =========================================
    // Autenticacao
    // =========================================

    /**
     * Pagina inicial — redireciona para login ou dashboard
     */
    public function index()
    {
        if ($this->session->userdata('cliente_logado')) {
            redirect('cliente/dashboard');
        } else {
            redirect('cliente/login');
        }
    }

    /**
     * Login do cliente
     * GET: mostra tela de login
     * POST: autentica email + senha
     */
    public function login()
    {
        if ($this->session->userdata('cliente_logado')) {
            redirect('cliente/dashboard');
            return;
        }

        $data = ['erro' => '', 'sucesso' => ''];
        $data['loja'] = $this->db->get('setting')->row();

        // Flash messages
        if ($this->session->flashdata('sucesso')) {
            $data['sucesso'] = $this->session->flashdata('sucesso');
        }

        if ($this->input->method() === 'post') {
            $email = $this->input->post('email', TRUE);
            $senha = $this->input->post('senha', TRUE);

            // Rate limiting
            $ip = $this->input->ip_address();
            $attempts = $this->Customer_portal_model->count_recent_attempts($email, $ip);
            if ($attempts >= 5) {
                $data['erro'] = 'Muitas tentativas. Aguarde 15 minutos.';
                $this->load->view('cliente/login', $data);
                return;
            }

            // Buscar cliente
            $customer = $this->Customer_portal_model->find_by_email($email);
            if (!$customer || empty($customer->password_hash)) {
                $this->Customer_portal_model->record_attempt($email, $ip, false);
                $data['erro'] = 'E-mail ou senha incorretos.';
                $this->load->view('cliente/login', $data);
                return;
            }

            // Verificar senha
            if (!password_verify($senha, $customer->password_hash)) {
                $this->Customer_portal_model->record_attempt($email, $ip, false);
                $data['erro'] = 'E-mail ou senha incorretos.';
                $this->load->view('cliente/login', $data);
                return;
            }

            // Login OK
            $this->Customer_portal_model->record_attempt($email, $ip, true);
            $this->Customer_portal_model->update_last_login($customer->id);

            $this->session->set_userdata([
                'cliente_logado'   => true,
                'cliente_id'       => (int) $customer->id,
                'cliente_nome'     => $customer->name,
                'cliente_email'    => $customer->email,
                'cliente_telefone' => $customer->mobile,
            ]);

            redirect('cliente/dashboard');
            return;
        }

        $this->load->view('cliente/login', $data);
    }

    /**
     * Registro de novo cliente
     */
    public function registrar()
    {
        if ($this->session->userdata('cliente_logado')) {
            redirect('cliente/dashboard');
            return;
        }

        $data = ['erro' => ''];
        $data['loja'] = $this->db->get('setting')->row();

        if ($this->input->method() === 'post') {
            $nome     = trim(strip_tags($this->input->post('nome', TRUE)));
            $email    = trim($this->input->post('email', TRUE));
            $telefone = trim($this->input->post('telefone', TRUE));
            $senha    = $this->input->post('senha');
            $confirma = $this->input->post('confirmar_senha');

            // Validacoes
            if (empty($nome) || empty($email) || empty($telefone) || empty($senha)) {
                $data['erro'] = 'Preencha todos os campos.';
                $this->load->view('cliente/registrar', $data);
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['erro'] = 'E-mail invalido.';
                $this->load->view('cliente/registrar', $data);
                return;
            }

            if (strlen($senha) < 6) {
                $data['erro'] = 'A senha deve ter no minimo 6 caracteres.';
                $this->load->view('cliente/registrar', $data);
                return;
            }

            if ($senha !== $confirma) {
                $data['erro'] = 'As senhas nao conferem.';
                $this->load->view('cliente/registrar', $data);
                return;
            }

            // Verificar email duplicado (com password_hash = conta ja existe)
            $existing_email = $this->Customer_portal_model->find_by_email($email);
            if ($existing_email && !empty($existing_email->password_hash)) {
                $data['erro'] = 'E-mail ja cadastrado. Faca login.';
                $this->load->view('cliente/registrar', $data);
                return;
            }

            $password_hash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);

            // Verificar se ja existe cliente com este telefone (de pedidos anteriores)
            $existing_phone = $this->Customer_portal_model->find_by_phone($telefone);

            if ($existing_phone && empty($existing_phone->password_hash)) {
                // Cliente ja existe sem conta — atualizar com email + senha
                // Se o email ja esta em uso por outro registro, rejeitar
                if ($existing_email && $existing_email->id !== $existing_phone->id) {
                    $data['erro'] = 'E-mail ja cadastrado por outro cliente.';
                    $this->load->view('cliente/registrar', $data);
                    return;
                }

                $this->Customer_portal_model->update_profile($existing_phone->id, [
                    'email'         => strtolower(trim($email)),
                    'password_hash' => $password_hash,
                    'name'          => $nome,
                ]);
                $customer_id = $existing_phone->id;
            } elseif ($existing_email && empty($existing_email->password_hash)) {
                // Email existe sem senha (registro antigo) — adicionar senha
                $this->Customer_portal_model->update_profile($existing_email->id, [
                    'password_hash' => $password_hash,
                    'name'          => $nome,
                    'mobile'        => $telefone,
                ]);
                $customer_id = $existing_email->id;
            } else {
                // Novo cliente
                $count = $this->db->count_all('customer_tbl');
                $customerid = 'Cus_' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

                $customer_id = $this->Customer_portal_model->create_account([
                    'customerid'    => $customerid,
                    'name'          => $nome,
                    'email'         => strtolower(trim($email)),
                    'mobile'        => $telefone,
                    'password_hash' => $password_hash,
                    'address'       => '',
                    'status'        => 1,
                    'created_by'    => 'portal_cliente',
                    'tipo_pessoa'   => 'F',
                ]);
            }

            // Vincular pedidos antigos pelo telefone
            $this->Customer_portal_model->link_orders_by_phone($customer_id, $telefone);

            // Auto-login apos registro
            $customer = $this->Customer_portal_model->find_by_id($customer_id);
            $this->Customer_portal_model->update_last_login($customer->id);

            $this->session->set_userdata([
                'cliente_logado'   => true,
                'cliente_id'       => (int) $customer->id,
                'cliente_nome'     => $customer->name,
                'cliente_email'    => $customer->email,
                'cliente_telefone' => $customer->mobile,
            ]);

            $this->session->set_flashdata('sucesso', 'Conta criada com sucesso!');
            redirect('cliente/dashboard');
            return;
        }

        $this->load->view('cliente/registrar', $data);
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->unset_userdata([
            'cliente_logado', 'cliente_id', 'cliente_nome',
            'cliente_email', 'cliente_telefone'
        ]);
        redirect('cliente/login');
    }

    // =========================================
    // Password Reset
    // =========================================

    /**
     * Esqueci minha senha — envia email com link
     */
    public function esqueci_senha()
    {
        $data = ['erro' => '', 'sucesso' => ''];
        $data['loja'] = $this->db->get('setting')->row();

        if ($this->input->method() === 'post') {
            $email = trim($this->input->post('email', TRUE));

            // Sempre mostrar a mesma mensagem (anti-enumeracao)
            $mensagem_generica = 'Se este e-mail estiver cadastrado, voce recebera um link para redefinir sua senha.';

            $customer = $this->Customer_portal_model->find_by_email($email);
            if ($customer && !empty($customer->password_hash)) {
                // Gerar token
                $plain_token = bin2hex(random_bytes(32));
                $token_hash  = hash('sha256', $plain_token);
                $expires_at  = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $this->Customer_portal_model->create_reset_token($customer->id, $token_hash, $expires_at);

                // Enviar email
                $reset_url = base_url('cliente/redefinir-senha/' . $plain_token);
                $this->_send_reset_email($customer->email, $customer->name, $reset_url);
            }

            $data['sucesso'] = $mensagem_generica;
        }

        $this->load->view('cliente/esqueci_senha', $data);
    }

    /**
     * Redefinir senha com token
     */
    public function redefinir_senha($token = null)
    {
        if (empty($token)) {
            redirect('cliente/login');
            return;
        }

        $data = ['erro' => '', 'token' => $token];
        $data['loja'] = $this->db->get('setting')->row();

        // Validar token
        $token_hash = hash('sha256', $token);
        $reset = $this->Customer_portal_model->find_valid_reset_token($token_hash);
        if (!$reset) {
            $data['erro'] = 'Link invalido ou expirado. Solicite um novo.';
            $data['token_invalido'] = true;
            $this->load->view('cliente/redefinir_senha', $data);
            return;
        }

        if ($this->input->method() === 'post') {
            $senha    = $this->input->post('senha');
            $confirma = $this->input->post('confirmar_senha');

            if (strlen($senha) < 6) {
                $data['erro'] = 'A senha deve ter no minimo 6 caracteres.';
                $this->load->view('cliente/redefinir_senha', $data);
                return;
            }

            if ($senha !== $confirma) {
                $data['erro'] = 'As senhas nao conferem.';
                $this->load->view('cliente/redefinir_senha', $data);
                return;
            }

            // Atualizar senha
            $hash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);
            $this->Customer_portal_model->update_password($reset->customer_id, $hash);
            $this->Customer_portal_model->mark_token_used($reset->id);

            $this->session->set_flashdata('sucesso', 'Senha alterada com sucesso! Faca login.');
            redirect('cliente/login');
            return;
        }

        $this->load->view('cliente/redefinir_senha', $data);
    }

    // =========================================
    // Dashboard
    // =========================================

    /**
     * Dashboard — visao geral
     */
    public function dashboard()
    {
        $this->_check_auth();

        $customer_id = (int) $this->session->userdata('cliente_id');
        $data['loja'] = $this->db->get('setting')->row();
        $data['customer'] = $this->Customer_portal_model->find_by_id($customer_id);
        $data['recent_orders'] = $this->Customer_portal_model->get_orders($customer_id, 5);
        $data['stats'] = $this->Customer_portal_model->get_stats($customer_id);

        if ($this->session->flashdata('sucesso')) {
            $data['sucesso'] = $this->session->flashdata('sucesso');
        }

        $this->load->view('cliente/dashboard', $data);
    }

    // =========================================
    // Pedidos
    // =========================================

    /**
     * Historico de pedidos
     */
    public function pedidos()
    {
        $this->_check_auth();

        $customer_id = (int) $this->session->userdata('cliente_id');
        $page = max(1, (int) $this->input->get('page'));
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        $status_filter = $this->input->get('status', TRUE);

        $data['loja'] = $this->db->get('setting')->row();
        $data['orders'] = $this->Customer_portal_model->get_orders($customer_id, $per_page, $offset, $status_filter ?: null);
        $data['total'] = $this->Customer_portal_model->get_order_count($customer_id, $status_filter ?: null);
        $data['page'] = $page;
        $data['per_page'] = $per_page;
        $data['total_pages'] = max(1, ceil($data['total'] / $per_page));
        $data['status_filter'] = $status_filter;

        $this->load->view('cliente/pedidos', $data);
    }

    /**
     * Detalhe de um pedido
     */
    public function pedido_detalhe($order_number = null)
    {
        $this->_check_auth();

        if (!$order_number) {
            redirect('cliente/pedidos');
            return;
        }

        $customer_id = (int) $this->session->userdata('cliente_id');
        $order = $this->Customer_portal_model->get_order_with_items($order_number, $customer_id);

        if (!$order) {
            // IDOR prevention — pedido nao pertence ao cliente
            redirect('cliente/pedidos');
            return;
        }

        $data['loja'] = $this->db->get('setting')->row();
        $data['order'] = $order;

        $this->load->view('cliente/pedido_detalhe', $data);
    }

    // =========================================
    // Perfil
    // =========================================

    /**
     * Editar perfil
     */
    public function perfil()
    {
        $this->_check_auth();

        $customer_id = (int) $this->session->userdata('cliente_id');
        $data['loja'] = $this->db->get('setting')->row();
        $data['customer'] = $this->Customer_portal_model->find_by_id($customer_id);
        $data['erro'] = '';
        $data['sucesso'] = '';

        if ($this->session->flashdata('sucesso')) {
            $data['sucesso'] = $this->session->flashdata('sucesso');
        }
        if ($this->session->flashdata('erro')) {
            $data['erro'] = $this->session->flashdata('erro');
        }

        if ($this->input->method() === 'post') {
            $nome     = trim(strip_tags($this->input->post('name', TRUE)));
            $telefone = trim($this->input->post('mobile', TRUE));
            $cpf      = preg_replace('/\D/', '', $this->input->post('cpf', TRUE));
            $cep      = preg_replace('/\D/', '', $this->input->post('cep', TRUE));
            $endereco = trim(strip_tags($this->input->post('address', TRUE)));
            $cidade   = trim(strip_tags($this->input->post('cidade', TRUE)));
            $estado   = trim(strip_tags($this->input->post('estado', TRUE)));

            if (empty($nome)) {
                $data['erro'] = 'Nome e obrigatorio.';
                $this->load->view('cliente/perfil', $data);
                return;
            }

            $update_data = [
                'name'    => $nome,
                'mobile'  => $telefone,
                'address' => $endereco,
            ];
            if ($cpf)    $update_data['cpf']    = $cpf;
            if ($cep)    $update_data['cep']    = $cep;
            if ($cidade) $update_data['cidade'] = $cidade;
            if ($estado) $update_data['estado'] = $estado;

            $this->Customer_portal_model->update_profile($customer_id, $update_data);

            // Atualizar sessao
            $this->session->set_userdata('cliente_nome', $nome);
            $this->session->set_userdata('cliente_telefone', $telefone);

            $this->session->set_flashdata('sucesso', 'Perfil atualizado!');
            redirect('cliente/perfil');
            return;
        }

        $this->load->view('cliente/perfil', $data);
    }

    /**
     * Alterar senha
     */
    public function alterar_senha()
    {
        $this->_check_auth();

        if ($this->input->method() !== 'post') {
            redirect('cliente/perfil');
            return;
        }

        $customer_id = (int) $this->session->userdata('cliente_id');
        $customer = $this->Customer_portal_model->find_by_id($customer_id);

        $senha_atual  = $this->input->post('senha_atual');
        $nova_senha   = $this->input->post('nova_senha');
        $confirma     = $this->input->post('confirmar_nova_senha');

        if (!password_verify($senha_atual, $customer->password_hash)) {
            $this->session->set_flashdata('erro', 'Senha atual incorreta.');
            redirect('cliente/perfil');
            return;
        }

        if (strlen($nova_senha) < 6) {
            $this->session->set_flashdata('erro', 'A nova senha deve ter no minimo 6 caracteres.');
            redirect('cliente/perfil');
            return;
        }

        if ($nova_senha !== $confirma) {
            $this->session->set_flashdata('erro', 'As senhas nao conferem.');
            redirect('cliente/perfil');
            return;
        }

        $hash = password_hash($nova_senha, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->Customer_portal_model->update_password($customer_id, $hash);

        $this->session->set_flashdata('sucesso', 'Senha alterada com sucesso!');
        redirect('cliente/perfil');
    }

    // =========================================
    // Pagamentos
    // =========================================

    /**
     * Historico de pagamentos
     */
    public function pagamentos()
    {
        $this->_check_auth();

        $customer_id = (int) $this->session->userdata('cliente_id');
        $data['loja'] = $this->db->get('setting')->row();
        $data['pix_charges'] = $this->Customer_portal_model->get_pix_charges($customer_id);
        $data['card_charges'] = $this->Customer_portal_model->get_card_charges($customer_id);

        $this->load->view('cliente/pagamentos', $data);
    }

    // =========================================
    // Metodos privados
    // =========================================

    /**
     * Verificar autenticacao — redireciona ao login se nao logado
     */
    private function _check_auth()
    {
        if (!$this->session->userdata('cliente_logado')) {
            redirect('cliente/login');
            exit;
        }
    }

    /**
     * Envia email de reset de senha
     */
    private function _send_reset_email($to_email, $to_name, $reset_url)
    {
        $this->load->library('email');

        // Configuracao basica (usa config padrao do CI)
        $this->email->from('noreply@inventro.com', 'Inventro');
        $this->email->to($to_email);
        $this->email->subject('Redefinir sua senha');

        $data = [
            'name'      => $to_name,
            'reset_url' => $reset_url,
        ];
        $body = $this->load->view('emails/password_reset', $data, TRUE);
        $this->email->message($body);

        if (!$this->email->send()) {
            log_message('error', 'Customer_portal: Falha ao enviar email de reset para ' . $to_email);
            log_message('error', $this->email->print_debugger(['headers']));
        }
    }
}
