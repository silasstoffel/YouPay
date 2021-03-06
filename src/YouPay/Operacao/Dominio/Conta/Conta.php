<?php

namespace YouPay\Operacao\Dominio\Conta;

use DateTimeImmutable;
use DomainException;
use YouPay\Operacao\Dominio\Carteira\Carteira;
use YouPay\Operacao\Dominio\CpfCnpj;
use YouPay\Operacao\Dominio\Email;

class Conta
{
    const TIPO_CONTA_COMUM   = 1;
    const TIPO_CONTA_LOGISTA = 2;

    private ?string $id;
    private string $titular;
    private Email $email;
    private CpfCnpj $cpfCnpj;
    private int $tipoConta;
    private ?string $celular = null;
    private string $senha;
    private DateTimeImmutable $criadaEm;
    private DateTimeImmutable $alteradaEm;
    private Carteira $carteira;

    public function __construct(
        string $titular,
        Email $email,
        CpfCnpj $cpfCnpj,
        int $tipoConta,
        string $senha,
        ?string $celular = null,
        ?string $id = null
    ) {
        $this->setId($id)
            ->setTitular($titular)
            ->setEmail($email)
            ->setCpfCnpj($cpfCnpj)
            ->setTipoConta($tipoConta)
            ->setSenha($senha)
            ->setCelular($celular);
    }

    public static function criarInstanciaComArgumentosViaString(
        $titular,
        $email,
        $cpfCnpj,
        $senha,
        $dataCriadaEm = null,
        $id = null,
        $celular = null
    ): Conta {
        $email    = new Email($email);
        $cpfCnpj  = new CpfCnpj($cpfCnpj);
        $conta = new Conta(
            $titular,
            $email,
            $cpfCnpj,
            self::TIPO_CONTA_COMUM,
            $senha,
            $celular,
            $id
        );
        if (!is_null($dataCriadaEm)) {
            $conta->setCriadaEm(new DateTimeImmutable($dataCriadaEm));
        }
        if (strlen($conta->getCpfCnpj()) === 14) {
            $conta->setTipoConta(self::TIPO_CONTA_LOGISTA);
        }
        return $conta;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of titular
     */
    public function getTitular()
    {
        return $this->titular;
    }

    /**
     * Set the value of titular
     *
     * @return  self
     */
    public function setTitular($titular)
    {
        $this->titular = $titular;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of cpfCnpj
     */
    public function getCpfCnpj()
    {
        return $this->cpfCnpj;
    }

    /**
     * Set the value of cpfCnpj
     *
     * @return  self
     */
    public function setCpfCnpj($cpfCnpj)
    {
        $this->cpfCnpj = $cpfCnpj;

        return $this;
    }

    /**
     * Get the value of tipoConta
     */
    public function getTipoConta()
    {
        return $this->tipoConta;
    }

    /**
     * Set the value of tipoConta
     *
     * @return  self
     */
    public function setTipoConta($tipoConta)
    {
        $tipos = [self::TIPO_CONTA_COMUM, self::TIPO_CONTA_LOGISTA];
        if (!in_array($tipoConta, $tipos)) {
            throw new DomainException('Tipo de conta inválido.');
        }
        $this->tipoConta = $tipoConta;

        return $this;
    }

    /**
     * Get the value of senha
     */
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * Set the value of senha
     *
     * @return  self
     */
    public function setSenha($senha)
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Get the value of criadaEm
     */
    public function getCriadaEm()
    {
        return $this->criadaEm;
    }

    /**
     * Set the value of criadaEm
     *
     * @return  self
     */
    public function setCriadaEm(DateTimeImmutable $criadaEm)
    {
        $this->criadaEm = $criadaEm;

        return $this;
    }

    /**
     * Get the value of alteradaEm
     */
    public function getAlteradaEm()
    {
        return $this->alteradaEm;
    }

    /**
     * Set the value of alteradaEm
     *
     * @return  self
     */
    public function setAlteradaEm($alteradaEm)
    {
        $this->alteradaEm = $alteradaEm;

        return $this;
    }

    /**
     * Get the value of celular
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * Set the value of celular
     *
     * @return  self
     */
    public function setCelular($celular)
    {
        if (strlen($celular)) {
            $this->celular = str_replace(['.', '-', '(', ')', ' '], '', $celular);
        }

        return $this;
    }

    public function checkDuplicidadeConta(RepositorioContaInterface $respositorioConta)
    {
        $mensagem = 'O %s informado já está sendo utilizado por outra conta.';
            if ($this->existeContaComEmail($respositorioConta)) {
            throw new DomainException(sprintf($mensagem, 'e-mail'), 400);
        }

        if ($this->existeContaComCpfCnpj($respositorioConta)) {
            throw new DomainException(sprintf($mensagem, 'CPF ou CNPJ'), 400);
        }
    }

    public function existeContaComEmail(RepositorioContaInterface $respositorioConta)
    {
        $conta = $respositorioConta->buscarPorEmail($this->getEmail());
        if ($conta && $conta->getId() !== $this->getId()) {
            return true;
        }
        return false;
    }

    public function existeContaComCpfCnpj(RepositorioContaInterface $respositorioConta)
    {
        $conta = $respositorioConta->buscarPorCpfCnpj($this->getCpfCnpj());
        if ($conta && $conta->getId() !== $this->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Verifica se a conta pode fazer transferencia.
     *
     * @return bool
     */
    public function fazTransferencia(): bool
    {
        return $this->getTipoConta() !== self::TIPO_CONTA_LOGISTA;
    }

    /**
     * Vincula de carteira com a conta.
     *
     * @param Carteira $carteira Instancia da carteira
     * @return void
     */
    public function vincularCarteira(Carteira $carteira)
    {
        $this->carteira = $carteira;
    }

    public function getCarteira() : Carteira
    {
        return $this->carteira;
    }
}
