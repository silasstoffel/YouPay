<?php

/**
 * @OA\Post(
 *     path="/contas",
 *     summary="Cria conta",
 *     description="Cria uma conta na plataforma YouPay.",
 *     tags={"Contas"},
 *     @OA\RequestBody(
 *         description="Auth",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/Conta")
 *     ),
 *     @OA\Response(
 *       response="201",
 *       description="Dados da conta criada",
 *       @OA\JsonContent(ref="#/components/schemas/Conta")
 *     ),
 *     @OA\Response(
 *       response="400",
 *       description="Detalhamento do erro.",
 *       @OA\JsonContent(ref="#/components/schemas/DefaultErrorResponse")
 *     )
 * )
 */
