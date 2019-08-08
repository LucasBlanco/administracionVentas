<?php
/**
 * Created by PhpStorm.
 * User: lblanco
 * Date: 08/03/19
 * Time: 17:03
 */

namespace App\services;


use App\Auditoria;
use App\Enums\Estados;
use App\Enums\Perfiles;
use App\services\Fecha\Fecha;
use App\services\DatosEmpresaService;
use App\services\ValidacionService;
use App\services\VisitaService;
use App\services\AuditoriaService;
use App\services\PromiseService;
use App\User;
use App\Validacion;
use App\Venta;
use App\Visita;
use Illuminate\Support\Facades\DB;


class VentaService
{

    //use Fecha;

    private $preVentaServer;

    public function __construct()
    {
        $this->preVentaServer = new PromiseService('https://jsonplaceholder.typicode.com');

    }

    public function ventasIncompletas()
    {
        return $this->preVentaServer->get('ventasIncompletas');
    }

    public function ventasPresentables()
    {
        return $this->preVentaServer->get('ventasPresentables');
    }

    public function ventasPresentadas()
    {
        return $this->preVentaServer->get('ventasPresentadas');
    }

    public function getById($id){
        return $this->preVentaServer->get('find/' . $id);
    }

    public function update($idVenta, $cuit, $empresa, $tresPorciento){
        $datosParaCompletar = (object) [
            'idVenta' => $idVenta,
            'cuit' => $cuit,
            'empresa' => $empresa,
            'tresPorciento' => $tresPorciento
        ];
        return $this->preVentaServer->post('update', $datosParaCompletar);
    }





}