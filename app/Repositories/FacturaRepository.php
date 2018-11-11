<?php
namespace App\Repositories;
use App\Models\Factura;
use App\Models\ComprobantePago;
use App\Http\Helpers\DateFormat;
	
class FacturaRepository extends BaseRepository {
    /**
     * The PersonaNatural instance.
     *
     * @var App\Models\PersonaNatural
     */
    protected $comprobantePago;

    /**
     * Create a new UsuarioRepository instance.
     * @param  App\Models\Usuario $usuario
     * @param  App\Models\PersonaNatural $personaNatural
     * @param  App\Models\TipoUsuario $tipoUsuario
     * @param  App\Models\Tienda $tienda
     * @return void
     */
    public function __construct(Boleta $factura=null, ComprobantePago $comprobantePago=null){
        $this->model = $factura;
        $this->comprobantePago = $comprobantePago;
    }

    protected function setComprobantePagoData($dataComprobantePago){
        $this->comprobantePago['subtotal'] =  $dataComprobantePago['subtotal'];
        $this->comprobantePago['deleted'] =  false; //default value
    }

    protected function setFacturaData($dataFactura){
        $this->model['igv'] =  $dataFactura['igv'];
        $this->model['deleted'] =  false; //default value
    }

    protected function saveComprobantePago(){
        $this->comprobantePago->save();    
    }

    protected function attachFacturaToComprobantePago($comprobantePago, $factura){
        return $comprobantePago->factura()->save($factura);
    }

    public function guarda($dataArray){
        $this->setComprobantePagoData($dataArray); //set data only in its ComprobantePago model
        $this->saveComprobantePago(); //saving in database
        $this->setFacturaData($dataArray);// set data only in its Usuario model
        $this->attachFacturaToComprobantePago($this->comprobantePago,$this->model);
        $this->model->comprobantePago;//loading comprobantePago
    }

    public function actualiza($dataArray){
        //persona natural no tiene atributos con el mismo nombre de atributos del usuario que se vayan a actualizar
        //deleted, created_at y updated_at son comunes, pero estos jamas se actualizaran por acá
        $this->comprobantePago->update($dataArray);
        $this->model->update($dataArray); //set data only in its ComprobantePago model
    }

    // public function actualizaSoloBoleta($dataArray){
    //     //persona natural no tiene atributos con el mismo nombre de atributos del usuario que se vayan a actualizar
    //     //deleted, created_at y updated_at son comunes, pero estos jamas se actualizaran por acá
    //     if (array_key_exists('igv',$dataArray))
    //         $this->model->update($dataArray); //set data only in its PersonaNatural model
    // }

    
    public function listarFacturas(){
        $lista = $this->model->where('deleted',false)->get();
        foreach ($lista as $key => $factura) {
            $factura->comprobantePago;
        }
        return $lista;
    }
    public function obtenerFacturaPorId($id){
        $factura = $this->model->where('idComprobantePago',$id)->where('deleted',false)->first();
        if($factura) $factura->comprobantePago;
        return $factura;
    }

    protected function setComprobantePagoModel($comprobantePago){
        $this->comprobantePago = $comprobantePago;
    }

    public function setModelFactura($factura){
        $this->model = $factura;
        $comprobantePago = $factura->comprobantePago;
        if($factura->comprobantePago)
            $this->comprobantePago =  $comprobantePago;
    }
    
    public function softDelete(){
        $this->comprobatePago->deleted = true;
        $this->comprobatePago->save();
        $this->model->deleted = true;
        $this->model->save();
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