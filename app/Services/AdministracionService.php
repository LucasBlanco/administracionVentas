<?php
/**
 * Created by PhpStorm.
 * User: joaquin
 * Date: 11/03/19
 * Time: 22:34
 */

namespace App\services;


use App\Enums\Estados;
use App\Enums\Perfiles;
//use App\services\Fecha\Fecha;
use App\User;
use App\Venta;

class AdministracionService
{
    //use Fecha;

    private $ventaSrv;
    private $estadoSrv;

    public function __construct()
    {
        $this->ventaSrv = new VentaService();
        //$this->estadoSrv = new EstadoService();
    }

    public function completarVenta($idVenta, $cuit, $empresa, $tresPorciento)
    {
        $this->ventaSrv->update(
            $idVenta,
            $empresa,
            $cuit,
            $tresPorciento
        );
    }

    public function presentarVentas($ventas, User $user, $fechaPresentacion)
    {
        $ventas->each(
            function ($v) use ($user, $fechaPresentacion) {
                $this->estadoSrv
                    ->crear(
                        $user,
                        $v,
                        Estados::PRESENTADA,
                        $fechaPresentacion,
                        true,
                        false

                    );
            }
        );
    }

    public function analizarPresentacion(Venta $venta, $estado, $recuperable, $observacion, User $user)
    {
        if ($estado == "PAGADA")
            $this->estadoSrv->crear($user, $venta, Estados::PAGADA, $this->todayStr(), $recuperable, false, $observacion);
        else if ($estado == "RECHAZADA")
            $this->estadoSrv->crear($user, $venta, Estados::RECHAZO_PRESENTACION, $this->todayStr(), $recuperable, false, $observacion);
        else {
            $es = $this->estadoSrv->findByEstado($venta, [Estados::PRESENTADA]);
            $this->estadoSrv->delete($es);
        }
    }

    public function ventasRechazables()
    {
        $v1 = Venta::whereDoesntHave(
            'estados',
            function ($q) {
                $q->where('estado', 'like', 'Rech%');
            }
        )
            ->whereHas(
                'estados',
                function ($q) {
                    $q->where('estado', Estados::VISITA_CONFIRMADA);
                }
            )
            ->with('obraSocial');

        $v2 = Venta::whereDoesntHave(
            'estados',
            function ($q) {
                $q->where('estado', 'like', 'Rech%');
            }
        )
            ->whereHas(
                'estados',
                function ($q) {
                    $q->where('estado', Estados::VALIDADO);
                }
            )
            ->whereHas(
                'estados',
                function ($q) {
                    $q->where('estado', Estados::CREADO)
                        ->whereHas(
                            'usuario.perfiles',
                            function ($q) {
                                        $q->where('nombre', Perfiles::EXTERNO)
                                            ->orWhere('nombre', Perfiles::PROMOTORA)
                                            ->orWhere('nombre', Perfiles::VENDEDORA);
                            });
                }
            )
            ->with('obraSocial');

        return $v1->union($v2)->get();
    }

    public function rechazar(Venta $venta, User $user, $recuperable, $observacion)
    {
        $this->estadoSrv->crear($user, $venta, Estados::RECHAZO_ADMINISTRACION, $this->todayStr(), $recuperable, false, $observacion);
    }
}