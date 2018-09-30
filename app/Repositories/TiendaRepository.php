<?php
namespace App\Repositories;
use App\Models\Tienda;
	
class TiendaRepository extends BaseRepository {
    /**
     * The Usuario instance.
     *
     * @var App\Models\Usuario
     */
    protected $jefeDeTienda;
    /**
     * The Usuario instance.
     *
     * @var App\Models\Comment
     */
    protected $jefeDeAmacen;
    /**
     * Create a new BlogRepository instance.
     *
     * @param  App\Models\Post $post
     * @param  App\Models\Tag $tag
     * @param  App\Models\Comment $comment
     * @return void
     */
    public function __construct(Tienda $tienda) 
    {
        $this->model = $tienda;
        
    }

    public function test(){
        return "test";
    }
    
}