<?php
namespace App\Repositories;
use App\Models\Factura;
use App\Models\ComprobantePago;
use App\Models\Usuario;
use App\Models\PersonaJuridica;
use App\Models\Categoria;
	
class FacturaRepository extends BaseRepository {
    protected $cliente;
    protected $comprobantePago;
    
    /** 
     * Create a new ProductoRepository instance.
     * @param  App\Models\Producto $producto
     * @param  App\Models\TipoProducto $tipoProducto
     * @param  App\Models\Proveedor $proveedor
     * @return void
     */
    public function __construct(Factura $factura, ComprobantePago $comprobantePago,PersonaJuridica $personaJuridica) 
    {
        $this->model = $factura;
        $this->comprobantePago = $comprobantePago;
        $this->personaJuridica = $personaJuridica;
       
        
    }

    /**
     * Save data from the array
     *
     * @return App\Models\Model
     */
    public function guarda($dataArray)
    {
        $dataArray['deleted'] =false;

        return $this->model = $this->model->create($dataArray);
        
    }

    public function loadComprobantePagoRelationship($factura=null){
    
        if (!$producto){
            $this->model = $this->model->load([
                'comprobantePago'=>function($query){
                    $query->where('deleted', false); 
                },
                'comprobantePago.usuario'=>function($query){
                    $query->where('usuario.deleted', false); 
                }
            ]);
        }
        else{
            
            $this->model =$factura->load([
                'comprobantePago'=>function($query){
                    $query->where('deleted', false); 
                },
                'comprobantePago.usuario'=>function($query){
                    $query->where('usuario.deleted', false); 
                }
            ]);
        }
        if ($this->model->comprobantePago){
            $this->comprobantePago = $this->model->comprobantePago;
        }
    }

    public function loadPersonaJuridicaRelationship($factura=null){
    
        if (!$producto){
            $this->model = $this->model->load([
                'personaJuridica'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        else{
            
            $this->model =$factura->load([
                'personaJuridica'=>function($query){
                    $query->where('deleted', false); 
                }
            ]);
        }
        if ($this->model->personaJuridica){
            $this->personaJuridica = $this->model->personaJuridica;
        }
    }

}