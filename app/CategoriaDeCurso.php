<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaDeCurso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorias_cursos';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = ['id', 'cvucv_category_parent_id', 'cvucv_category_super_parent_id', 'cvucv_name', 'cvucv_description','cvucv_coursecount', 'cvucv_visible', 'cvucv_depth','cvucv_path', 'cvucv_link','periodo_lectivo_id','created_at','updated_at'];

    public static function create($id, $parent, $name, $description, $coursecount, $visible, $depth, $path)    {
        $new = new self();

        $new->id                            = $id;
        $new->cvucv_category_parent_id      = $parent;
        $new->cvucv_name                    = $name;
        $new->cvucv_description             = $description;
        $new->cvucv_coursecount             = $coursecount;
        $new->cvucv_visible                 = $visible;
        $new->cvucv_depth                   = $depth;
        $new->cvucv_path                    = $path;
        
        return $new;
    }

    public function cursos(){
        return $this->hasMany('App\Curso','cvucv_category_id','id');
    }

    public function instrumentos_habilitados(){
        return $this->belongsToMany('App\Instrumento','instrumentos_habilitados','categoria_id','instrumento_id')->using('App\InstrumentosHabilitados');
    }
    public function categoria_raiz(){
        return $this->belongsTo('App\CategoriaDeCurso','cvucv_category_super_parent_id','id');
    }
    public function categoria_raiz_hijos(){
        return $this->hasMany('App\CategoriaDeCurso','cvucv_category_super_parent_id','id');
    }

    public function disponible_para_evaluar(){

        if(!$this->instrumentos_habilitados->isEmpty){
            return true;
        }
        return false;
    }

    public function periodo_lectivo_actual()    {
        return $this->belongsTo('App\PeriodoLectivo','periodo_lectivo_id','id');
    }

    public function getPeriodoLectivo(){
        return $this->periodo_lectivo_id;
    }
    public function setPeriodoLectivo($periodo){
        $this->periodo_lectivo_id = $periodo;
        $this->save();
    }

    public function getNombre(){
        return $this->cvucv_name;
    }
    public function getCVUCV_NAME(){
        return $this->cvucv_name;
    }

    public function getID(){
        return $this->id;
    }

    public static function getCategoriaPorNombre($nombre){
        if($nombre == null){return null;}

        $categoria = CategoriaDeCurso::where('cvucv_name',$nombre)->first();

        if(!empty($categoria)){
            return $categoria->getID();
        }

        return null;
    }

    public static function CategoriaPorNombre($nombres = []){
        if($nombres == []){
            return [];
        }
        $list = [];
        $i=0;
        foreach($nombres as $index => $nombre){
            if($nombre){
                if(!empty($categoria = CategoriaDeCurso::where('cvucv_name',$nombre)->first()) ){
                    $list[$i] = $categoria;
                    $i++;
                }
            }
        }
        return $list;
    }

    
}
