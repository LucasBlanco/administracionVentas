<?php

namespace App\Http\Controllers;

use App\services\AdministracionService;
use App\services\VentaService;
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
        $this->ventasSrv = new VentaService();
        $this->userSrv = new UsuarioService();
        $this->promiseSrv = new PromiseService();
    }

    public function ventasIncompletas()
    {
        $promise = $this->ventasSrv->ventasIncompletas();
        return $promise->wait()->getBody();
    }

    public function ventasPresentables()
    {
        $promise = $this->ventasSrv->ventasPresentables();
        return $promise->wait()->getBody();
    }

    public function ventasPresentadas()
    {
        $promise = $this->ventasSrv->ventasPresentadas();
        return $promise->wait()->getBody();
    }

    public function ventasRechazables()
    {
        $promise = $this->ventasSrv->ventasRechazables();
        return $promise->wait()->getBody();
    }

    public function rechazar(Request $request)
    {
        $ventaPromise = $this->ventasSrv->getById($request['idVenta']);
        $usuarioPromise = $this->userSrv->getById($request['userId']);
        $respuesta = $this->promiseSrv->all($ventaPromise, $usuarioPromise)->wait()->getBody();
        $venta = $respuesta[0];
        $user = $respuesta[1];

        DB::transaction(function() use ($request, $venta, $user){
            $recuperable = $request['recuperable'];
            $observacion = $request['observacion'];
            $this->service->rechazar($venta, $user, $recuperable, $observacion);
        });
    }

    public function completarVenta(Request $request)
    {   $idVenta = $request['idVenta'];
        $cuit = $request['cuit'];
        $empresa = $request['empresa'];
        $tresPorciento = $request['tresPorciento'];
        $this->service->completarVenta($idVenta, $cuit, $empresa, $tresPorciento);
    }

    public function presentarVentas(Request $request)
    {
        Db::transaction(function() use ($request){
            $ventas = Venta::whereIn('id', $request['ids'])->get();
            $user = User::find($request['userId']);
            $fechaPresentacion = $request['fechaPresentacion'];
            $this->service->presentarVentas($ventas, $user, $fechaPresentacion);
        });
    }

    public function analizarPresentacion(Request $request)
    {
        return Db::transaction(function() use ($request){
            $venta = Venta::find($request['idVenta']);
            $user = User::find($request['userId']);
            $estado = $request['estado'];
            $recuperable = $request['recuperable'];
            $observacion = $request['observacion'];
            return $this->service->analizarPresentacion($venta, $estado, $recuperable, $observacion, $user);
        });
    }


}
