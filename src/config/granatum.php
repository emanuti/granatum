<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Granatum
    |--------------------------------------------------------------------------
    |
    | Config file
    */

    'env'        => 'dev', // dev|prod
    'endpoint'   => 'https://api.granatum.com.br/v1/',
    'token'      => [
        'dev'  => '',
        'prod' => ''
    ],

    /**
     * Cobranças
     */
    'conta_id'        => '',
    'data_vencimento' => today()->addDays(30)->format('Y-m-d'),
    'tipo_emissao'    => 1,
    'tipo_cobranca'   => 'boleto',
    'permitir_segunda_via' => 1,
    'percentual_multa'   => 2.0,
    'itens' => [
        'categoria' => [
            [
                ''
            ]
        ]
    ]
];
