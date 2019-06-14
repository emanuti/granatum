<?php

namespace Emanuti\Granatum;

/**
 * Granatum
 * https://www.granatum.com.br/financeiro/api/
 */
class Granatum 
{

    /**
     * Required at all POST and PUT requests
     */
    const POST_PUT_HEADER = ['Content-Type: application/x-www-form-urlencoded'];
    const CHILDREN_KEYS   = [
        'categorias_filhas',
        'centros_custo_lucro_filhos',
        'itens',
    ];

    private $routes = [
        // 'lancamentos', // needed conta_id
        // 'transferencias',
        // 'cobrancas', // needed (data_inicio|data_fim|conta_id|cliente_id|cobranca_hash)
        'categorias',
        'clientes',
        'fornecedores',
        'contas',
        'centros_custo_lucro',
        'formas_pagamento',
    ];

    private $vo_routes = [
        'tipos_documento_fiscal',
        'tipos_custo_nivel_producao',
        'tipos_custo_apropriacao_produto',
        'cidades',
        'estados',
        'bancos',
    ];

    private $env = '';
    private $errors = [];

    /**
     * Set env to use proper Token
     * 
     * @param boolean $value
     */
    public function setEnv(string $env = 'prod')
    {
        $this->env = strtolower($env);
    }
    
    /**
     * Get Enviroment currently used
     */
    public function getEnv()
    {
        if(empty($this->env)) {
            $this->setEnv(config('granatum.env'));
        }
        return $this->env;
    }

    /**
     * Get items/item related with route
     * 
     * @param string $route https://www.granatum.com.br/financeiro/api/
     * @param int $id
     * 
     * @return Illuminate\Support\Collection
     */
    public function get(string $route, int $id = null)
    {
        if(!empty($id)) {
            $route .= '/' . $id;
        }

        $handle = $this->bootstrapCurl($route, null, 'GET');
        return $this->handleToCollection($handle);
    }

    /**
     * Create something creatable according Granatum api
     * 
     * @param string $route https://www.granatum.com.br/financeiro/api/
     * @param array $fields
     * 
     * @return Illuminate\Support\Collection
     */
    public function post(string $route, array $fields)
    {
        $handle = $this->bootstrapCurl($route, $fields);
        return $this->handleToCollection($handle);
    }

    /**
     * Edit something editable according Granatum api
     * 
     * @param string $route https://www.granatum.com.br/financeiro/api/
     * @param array $fields
     * 
     * @return Illuminate\Support\Collection
     */
    public function put(string $route, int $id, array $fields)
    {
        $handle = $this->bootstrapCurl("$route/$id", $fields, 'PUT');
        return $this->handleToCollection($handle);
    }

    /**
     * Delete something deleteable according Granatum api
     * 
     * @param string $route https://www.granatum.com.br/financeiro/api/
     * @param int $id
     * 
     * @return Illuminate\Support\Collection
     */
    public function delete(string $route, int $id)
    {
        $handle = $this->bootstrapCurl("$route/$id", null, 'DELETE');
        return $this->handleToCollection($handle);
    }

    /**
     * Init cURL with default options seted on curl_setopt()
     * 
     * @param string $route https://www.granatum.com.br/financeiro/api/
     * @param array $fields 
     * @param string $method POST|PUT
     * 
     * @return cURL $handle
     */
    private function bootstrapCurl(string $route, array $fields = null, string $method = 'POST')
    {
        $handle = curl_init();
        
        curl_setopt($handle, CURLOPT_URL, $this->url($route));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        if($fields) {
            curl_setopt($handle, CURLOPT_HTTPHEADER, self::POST_PUT_HEADER);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $this->serializeCurlFields($fields));
        }

        return $handle;
    }

    /**
     * Url to access api
     * 
     * @return string $url
     */
    public function url($route)
    {
        $url = sprintf('%s%s?access_token=%s',
            config('granatum.endpoint'),
            $route,
            config('granatum.token.' . $this->getEnv())
        );

        return $url;
    }

    /**
     * Filter by property and value returned by route
     * 
     * @param string $route
     * @param string $property
     * @param string $value_property
     * 
     * @return StdClass
     */
    public function filter(string $route, string $property, string $value_property, bool $firstResult = true)
    {
        $that = $this;
        $collection = $this->get($route);

        $collection = $this->deepSearch($collection, $property, $value_property);
        
        if($firstResult) {
            return $collection->first();
        }
        return $collection;
    }

    /**
     * Search for property/value pair at all level of collection 
     * 
     * @param Collection $collection
     * @param string $property
     * @param string $value_property
     * 
     * @return Collection
     */
    private function deepSearch($collection, $property, $value_property)
    {
        $that = $this;
        $resultCollection = collect();
        
        $collection->each(function($item, $key) use($that, $resultCollection, $property, $value_property) {
            if($intersected = array_intersect_key( get_object_vars($item), array_flip(self::CHILDREN_KEYS) ) ) {
                $intersectedKey = key($intersected);
                if(count($item->$intersectedKey)) {
                    $result = $that->deepSearch(collect($item->$intersectedKey), $property, $value_property);
                    if($result->isNotEmpty()) {
                        $resultCollection->push($result);
                        return false;
                    }
                }
            }
            
            if(is_numeric(mb_stripos($item->$property, $value_property))) {
                $resultCollection->push($item);
                return false;
            }
        });
        
        return $resultCollection;
    }

    /**
     * Serialize fields to fill curl_setopt($handle, CURLOPT_POSTFIELDS, $fields)
     * 
     * @param array $fields
     */
    private function serializeCurlFields(array $fields)
    {
        return preg_replace('/%5B\d+%5D/','%5B%5D', http_build_query($fields));
    }

    private function addErrors($error)
    {
        array_push($this->errors, $error);
    }

    /**
     * Get handle and transform to Collection
     * 
     * @param json $handle ( curl_init() return )
     * @return Illuminate\Support\Collection
     */
    private function handleToCollection($handle)
    {
        $json  = curl_exec($handle);
        $array = json_decode($json);

        $collection = collect($array);
        if($json === false) {
            $this->addErrors(curl_error($handle));
        }
        if($collection->has('errors')) {
            $this->addErrors($collection->get('errors'));
        }
        
        curl_close($handle);

        return $collection;
    }
}
