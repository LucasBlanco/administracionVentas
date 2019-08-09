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


class VentaServiceGateway
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

    public function ventasRechazables()
    {
        return $this->preVentaServer->get('ventasRechazables');
    }

    public function presentarVentas($ventas, $user, $fechaPresentacion)
    {
        $presentaciones = (object)[
            'idVentas' => $ventas,
            'fechaPresentacion' => $fechaPresentacion,
            'idUser' => $user
        ];
        return $this->preVentaServer->post('presentarVentas', $presentaciones);
    }

    public function getById($id)
    {
        return $this->preVentaServer->get('find/' . $id);
    }

    public function analizarPresentacion($venta, $estado, $recuperable, $observacion, $user)
    {
        $presentacion = (object)[
            'idVenta' => $venta,
            'recuperable' => $recuperable,
            'observacion' => $observacion,
            'idUser' => $user
        ];
        if ($estado == "PAGADA")
            return $this->preVentaServer->post('pagarVentaPresentacion', $presentacion);
        else if ($estado == "RECHAZADA")
            return $this->preVentaServer->post('rechazarVentaPresentacion', $presentacion);
        else {
            return $this->preVentaServer->post('pendienteAuditoriaPresentacion', $presentacion);
        }
    }

    public function completarVenta($idVenta, $cuit, $empresa, $tresPorciento)
    {
        $datosParaCompletar = (object)[
            'cuit' => $cuit,
            'empresa' => $empresa,
            'tresPorciento' => $tresPorciento
        ];
        return $this->preVentaServer->put('update/' . $idVenta, $datosParaCompletar);
    }

    public function rechazar($venta, $user, $recuperable, $observacion)
    {
        $presentacion = (object)[
            'idVenta' => $venta,
            'recuperable' => $recuperable,
            'observacion' => $observacion,
            'idUser' => $user
        ];
        return $this->preVentaServer->post('rechazarVentaPresentacion', $presentacion);
    }

}