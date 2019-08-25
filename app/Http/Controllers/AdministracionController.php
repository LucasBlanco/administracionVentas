<?php

namespace App\Http\Controllers;

use App\services\AdministracionService;
use App\services\VentaServiceGateway;
use App\services\UsuarioService;
use App\services\PromiseService;
use App\User;
use App\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdministracionController extends Controller
{
    private $service;
    private $ventasSrv;
    private $userSrv;
    private $promiseSrv;

    public function __construct()
    {
        $this->service = new AdministracionService();
        $this->ventasSrv = new VentaServiceGateway();
        $this->userSrv = new UsuarioService();
        $this->promiseSrv = new PromiseService();
    }

    public function rechazar(Request $request)
    {
        $venta = $request['idVenta'];
        $user = $request['userId'];
        $recuperable = $request['recuperable'];
        $observacion = $request['observacion'];
        return $this->ventasSrv->rechazar($venta, $user, $recuperable, $observacion)->wait()->getBody();
    }

    public function completarVenta(Request $request)
    {
        $idVenta = $request['idVenta'];
        $cuit = $request['cuit'];
        $empresa = $request['empresa'];
        $tresPorciento = $request['tresPorciento'];
        return $this->ventasSrv->completarVenta($idVenta, $cuit, $empresa, $tresPorciento)->wait()->getBody();
    }

    public function presentarVentas(Request $request)
    {
        $ventas = $request['ids'];
        $user = $request['userId'];
        $fechaPresentacion = $request['fechaPresentacion'];
        return $this->ventasSrv->presentarVentas($ventas, $user, $fechaPresentacion)->wait()->getBody();
    }

    public function analizarPresentacion(Request $request)
    {

        $venta = $request['idVenta'];
        $user = $request['userId'];
        $estado = $request['estado'];
        $recuperable = $request['recuperable'];
        $observacion = $request['observacion'];
        return $this->ventasSrv->analizarPresentacion($venta, $estado, $recuperable, $observacion, $user)->wait()->getBody();
    }


}
