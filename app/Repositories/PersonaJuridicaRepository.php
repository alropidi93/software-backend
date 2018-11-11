<?php
namespace App\Repositories;
use App\Models\Factura;
use App\Models\ComprobantePago;
use App\Models\Usuario;
use App\Models\PersonaJuridica;
use App\Models\Categoria;
	
class PersonaJuridicaRepository extends BaseRepository {
    // protected $cliente;
    // protected $comprobantePago;
    
    /** 
     * Create a new ProductoRepository instance.
     * @param  App\Models\Producto $producto
     * @param  App\Models\TipoProducto $tipoProducto
     * @param  App\Models\Proveedor $proveedor
     * @return void
     */
    public function __construct(Factura $factura) 
    {
        $this->model = $personaJuridica;
        // $this->comprobantePago = $comprobantePago;
        // $this->personaJuridica = $personaJuridica;
       
        
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

   

}