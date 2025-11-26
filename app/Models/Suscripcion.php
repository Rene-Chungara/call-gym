<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    use HasFactory;
    protected $table = 'suscripcion';
    
    protected $fillable = [
        'usuario_id',
        'membresia_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'estado_pago',
        'tipo_pago',
        'fecha_estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'fecha_estado' => 'date',
        'estado'       => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function membresia()
    {
        return $this->belongsTo(Membresia::class, 'membresia_id');
    }

    public function detallesPago()
    {
        return $this->hasMany(DetallePago::class, 'suscripcion_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'suscripcion_id');
    }

    public function planesPago()
    {
        return $this->hasMany(PlanPago::class, 'suscripcion_id');
    }

    public function obtenerTipoPago()
    {
        if ($this->planesPago()->exists()) {
            return 'credito';
        }
        return 'contado';
    }

    public function obtenerEstadoPago()
    {
        // Si es crédito, verificar estado de cuotas
        if ($this->tipo_pago === 'credito') {
            // Cargar relaciones si no están cargadas
            if (!$this->relationLoaded('planesPago')) {
                $this->load('planesPago.cuotas');
            }
            
            $planActivo = $this->planesPago->where('estado', 'activo')->first();
            if ($planActivo) {
                $cuotasPendientes = $planActivo->cuotas->where('estado', '!=', 'pagado')->count();
                return $cuotasPendientes > 0 ? 'pendiente' : 'pagado';
            }
            return 'pendiente';
        }
        
        // Si es contado, usar estado_pago
        return $this->estado_pago ? 'pagado' : 'pendiente';
    }

    public function obtenerMontoTotal()
    {
        return $this->membresia->precio;
    }

    public function obtenerMontoPagado()
    {
        if ($this->tipo_pago === 'credito') {
            // Cargar relaciones si no están cargadas
            if (!$this->relationLoaded('planesPago')) {
                $this->load('planesPago.cuotas');
            }
            
            $planActivo = $this->planesPago->first();
            if ($planActivo) {
                return $planActivo->cuotas->where('estado', 'pagado')->sum('monto');
            }
            return 0;
        }
        
        // Cargar relaciones si no están cargadas
        if (!$this->relationLoaded('pagos')) {
            $this->load('pagos');
        }
        
        return $this->pagos->sum('monto_abonado');
    }

    public function obtenerMontoPendiente()
    {
        return $this->obtenerMontoTotal() - $this->obtenerMontoPagado();
    }
}

