<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instrumento;
use App\Curso;
use App\CategoriaDeCurso;
use App\CursoParticipante;
use App\PeriodoLectivo;
use App\Evaluacion;
use App\User;
use App\Charts\indicadoresChart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use \TCG\Voyager\Models\Permission;
use \TCG\Voyager\Models\Role;

class ControllerCustomGates extends Controller
{

    /**
     * Custom Sisgeva Gates
     *
     */


    //Para revisar si el usuario es admin
    public function checkCategoryPermissionSisgeva(User $user, $permiso, $category_name ){
        $string = $category_name;

        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', '- '=>'' );
        
        $string = strtr( $string, $unwanted_array );
        $string = (str_replace(' ', '_', strtolower($string)));
        

        if(!($user->roles->permisos->where('key',$permiso.$string)->isEmpty())){
            return true;
        }else{
            if($this->isNewCategory($string)){
                if(!($user->roles->permisos->where('key',$permiso.'otras_dependencias')->isEmpty())){
                    return true;
                }
            }
        }

        return false;
    }

    public function isNewCategory ($category_name){
        
        $list_dependencias=array(
            "facultad_de_arquitectura_y_urbanismo"          => "Facultad de Arquitectura y Urbanismo",
            "facultad_de_agronomia"                         => "Facultad de Agronomía",
            "facultad_de_ciencias"                          => "Facultad de Ciencias",
            "facultad_de_ciencias_juridicas_y_politicas"    => "Facultad de Ciencias Jurídicas y Políticas",
            "facultad_de_ciencias_economicas_y_sociales"    => "Facultad de Ciencias Económicas y Sociales",
            "facultad_de_farmacia"                          => "Facultad de Farmacia",
            "facultad_de_humanidades_y_educacion"           => "Facultad de Humanidades y Educación",
            "facultad_de_ingenieria"                        => "Facultad de Ingeniería",
            "facultad_de_medicina"                          => "Facultad de Medicina",
            "facultad_de_odontologia"                       => "Facultad de Odontología",
            "facultad_de_ciencias_veterinarias"             => "Facultad de Ciencias Veterinarias",
            "rectorado"                                     => "Rectorado",
            "vicerrectorado_academico"                      => "Vicerrectorado Académico",
            "vicerrectorado_administrativo"                 => "Vicerrectorado Administrativo",
            "secretaria"                                    => "Secretaría",
            "fundaciones_asociaciones"                      => "Fundaciones - Asociaciones"
        );

        foreach($list_dependencias as $name=>$display_name){
            if($category_name == $name){
                return false;
            }
        }
        return true;
    }
    
}
