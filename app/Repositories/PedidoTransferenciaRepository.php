<?php
namespace App\Repositories;
use App\Models\PedidoTransferencia;
use App\Models\LineaPedidoTransferencia;
use App\Models\Transferencia; ///no poner esta linea de mierda me hizo perder 2 horas :')
	
class PedidoTransferenciaRepository extends BaseRepository {
    protected $lineaPedidoTransferencia;
    protected $transferencia;
   
    /**
     * Create a new PedidoTransferencia instance.
     * @return void
     */
    public function __construct(PedidoTransferencia $pedidoTransferencia, LineaPedidoTransferencia $lineaPedidoTransferencia, Transferencia $transferencia=null){
        $this->model = $pedidoTransferencia;
        $this->lineaPedidoTransferencia = $lineaPedidoTransferencia;
        $this->transferencia = $transferencia;
    }

    public function loadTransferenciaRelationship($pedidoTransferencia=null){
        if (!$pedidoTransferencia){
                  

            $this->model = $this->model->load([
                'transferencia'=>function($query){
                    $query->where('transferencia.deleted', false);
                }
            ]);
        }
        else{
            
            $this->model =$pedidoTransferencia->load([
                'transferencia'=>function($query){
                    $query->where('transferencia.deleted', false); 
                }
            ]);
            
        }
        if ($this->model->transferencia && !$pedidoTransferencia){
            $this->transferencia = $this->model->transferencia;
        }
    }

    public function buscarPorFiltroPorTransferencia($key, $value){
        return $this->model->whereHas('transferencia',function ($q) use($key,$value){
            $q->whereRaw("lower({$key}) like ? ",'%'.$value.'%')->where('deleted',false);
        })->where('deleted',false)->get();

    }
    
}