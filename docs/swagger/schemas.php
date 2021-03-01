<?php

#-------------------------------------------------------------------------------
############################## DefaultErrorResponse ##############################
#-------------------------------------------------------------------------------

/**
 * @OA\Schema(
 *   schema="DefaultErrorResponse",
 *   title="DefaultErrorResponse",
 *   @OA\Property(property="error", type="boolean", description="Sinalizador para informar se houve erro."),
 *   @OA\Property(property="message", type="string", description="Mensagem com detalhe do erro.")
 * )
 */


#-------------------------------------------------------------------------------
############################## Conta ##############################
#-------------------------------------------------------------------------------

/**
 * @OA\Schema(
 *   schema="Conta",
 *   title="Conta",
 *   required={"cpfcnpj", "titular", "email", "senha"},
 *   @OA\Property(property="id", type="string", description="ID universal. Atributo apenas para leitura."),
 *   @OA\Property(property="cpfcnpj", type="string", description="CPF ou CNPJ."),
 *   @OA\Property(property="tipo_conta", type="integer", description="Tipo de conta, sendo 1 - Conta comum | 2 - Conta logista. Atributo apenas para leitura."),
 *   @OA\Property(property="titular", type="string", description="Nome do titular."),
 *   @OA\Property(property="email", type="string", description="Endereço de e-mail."),
 *   @OA\Property(property="celular", type="string", description="Numero de celular com DDD."),
 *   @OA\Property(property="senha", type="string", description="Senha para acesso a conta. Em ações de leitura a senha será omitida por ser dado sensível.")
 * )
 */
