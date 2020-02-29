<?php

use Illuminate\Database\Seeder;
use App\Indicador;
use App\Categoria;
use App\Instrumento;
use App\CursoParticipanteRol;

class GenetviInstrumentosPredeterminados extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Indicador::query()->delete();
        Categoria::query()->delete();
        Instrumento::query()->delete();

        //Indicadores - Dimensión Perfil de Usuario
        $indicadores[0]=array(
            "¿Ha tenido experiencia participando en otros Entornos Virtuales de Aprendizaje en la UCV?" => "likert",
            "¿Dispone de un computador?" => "likert",
            "¿Dispone de acceso a internet que le permita participar en el Entorno Virtual de Aprendizaje?" => "likert",
            "¿Dispones un dispositivo móvil inteligente con acceso a internet?" => "likert",
        );
        //Indicadores - Dimensión Académica - Componente Docencia
        $indicadores[1]=array(
            "El docente informa sobre el perfil de ingreso, requerimientos académicos y técnicos necesarios para participar en el Entorno Virtual de Aprendizaje." => "likert",
            "Se realizan planes de inducción, preparación y apoyo a los estudiantes para participar en el Entorno Virtual de Aprendizaje." => "likert",
            "Se promueve la interacción entre estudiantes a lo largo de las actividades del Entorno Virtual de Aprendizaje." => "likert",
            "Se promueve la interacción entre estudiantes y docentes a lo largo de las actividades del Entorno Virtual de Aprendizaje." => "likert",
            "Se utilizan repositorios digitales, bibliotecas virtuales y recursos educativos abiertos." => "likert",
            "Se realizan análisis para medir el grado de satisfacción de los estudiantes." => "likert",
            "Se presentan los criterios y procedimientos de evaluación de los aprendizajes acordes a la modalidad." => "likert",
            "Se diseñan actividades accesibles para todos (inclusión de personas con discapacidad)." => "likert",
            "Se hace uso de políticas y actividades que permitan la inclusión de todos los estudiantes." => "likert",
            "Se promueve el trabajo independiente para todos." => "likert",
            "Se dispone de planes de tutoría y atención a los estudiantes y apoyo en el proceso de aprendizaje." => "likert",
        );
        //Indicadores - Dimensión Académica - Componente Docencia
        $indicadores[2]=array(   
            "Se dispone de medios alternativos para publicar los contenidos y realización de actividades para estudiantes que no disponen de acceso permanente a internet o de conexiones de baja velocidad." => "likert",
            "Se presenta toda la información necesaria al estudiantado para su participación en el Entorno Virtual de Aprendizaje, tal como objetivos, competencias, contenidos, actividades, evaluación." => "likert",
            "Los docentes usan estrategias de acompañamiento a los estudiantes en los procesos de enseñanza y aprendizaje." => "likert",
            "Los contenidos son vigentes, actualizados y acordes a los estudiantes, así como también, coherente con los objetivos y competencias del Entorno Virtual de Aprendizaje." => "likert",
            "Se fomenta la utilización de licencias (creative commons, copyright, entre otras) para la publicación de los contenidos en el Entorno Virtual de Aprendizaje." => "likert",
            "Se consideran e incorporan las tendencias e innovaciones en EaD." => "likert",
        );
        //Indicadores - Dimensión Tecnológica - Componente Plataforma e Infraestructura Tecnológica
        $indicadores[3]=array(   
            "El Campus Virtual UCV cumple los estándares de accesibilidad para que cualquier usuario pueda tener acceso." => "likert"
        );
        //Indicadores - Dimensión Tecnológica - Componente Campus Virtual y Herramientas Tecnológicas
        $indicadores[4]=array(   
            "El acceso al Campus Virtual UCV está disponible las 24 horas los 7 días de la semana.",
            "El Sistema de Educación a Distancia de la UCV cuenta con personal calificado para soporte técnico y ayuda a los docentes y estudiantes." => "likert",
            "El Sistema de Educación a Distancia de la UCV dispone de personal especializado para ofrecer espacios de atención y asesoría técnica a estudiantes con discapacidad." => "likert",
            "El Campus Virtual UCV tiene organización y un diseño homogéneo que facilita la navegación en el curso en línea." => "likert"
        );

        //Indicadores - Dimensión Académica - Componente Docencia (Docentes)
        $indicadores[5]=array(
            "El Docente evidencia experiencia en la modalidad de EaD." => "likert",
            "Se presenta el diseño instruccional del Entorno Virtual de Aprendizaje adaptado a la modalidad a EaD." => "likert",
        );
        //Indicadores - Dimensión Tecnológica - Componente Campus Virtual y Herramientas Tecnológicas (Docentes)
        $indicadores[6]=array(
            "El Sistema de Educación a Distancia de la UCV ofrece un sistema para respaldar los datos de los cursos en línea y sus usuarios." => "likert" 
        );
        //Indicadores - 
        $indicadores[7]=array(
            "Observaciones generales" => "text_area" 
        );

        $categoriasTodas[0]=array("Perfil de Usuario"                       => "Dimensión Perfil de Usuario");
        $categoriasTodas[1]=array("Componente Estudiantil"                  => "Dimensión Académica - Componente Estudiantil");
        $categoriasTodas[2]=array("Componente Docencia"                     => "Dimensión Académica - Componente Docencia");
        $categoriasTodas[3]=array("Componente Infraestructura Tecnológica"  => "Dimensión Tecnológica - Componente Plataforma e Infraestructura Tecnológica");
        $categoriasTodas[4]=array("Componente Campus Virtual"               => "Dimensión Tecnológica - Componente Campus Virtual y Herramientas Tecnológicas");
        $categoriasTodas[5]=array("Componente Docencia"                     => "Dimensión Académica - Componente Docencia (Docentes)");
        $categoriasTodas[6]=array("Componente Campus Virtual"               => "Dimensión Tecnológica - Componente Campus Virtual y Herramientas Tecnológicas (Docentes)");
        $categoriasTodas[7]=array("Observaciones Generales"                 => "Observaciones Generales");

        //Lista de categorias1
        $categorias1=array(
            "Perfil de Usuario"                         => "Dimensión Perfil de Usuario",
            "Componente Estudiantil"                    => "Dimensión Académica - Componente Estudiantil",
            "Componente Docencia"                       => "Dimensión Académica - Componente Docencia",
            "Componente Infraestructura Tecnológica"    => "Dimensión Tecnológica - Componente Plataforma e Infraestructura Tecnológica",
            "Componente Campus Virtual"                 => "Dimensión Tecnológica - Componente Campus Virtual y Herramientas Tecnológicas",
        );
        //Lista de categorias2
        $categorias2=array(  
            "Componente Docencia"                       => "Dimensión Académica - Componente Docencia (Docentes)",
            "Componente Campus Virtual"                 => "Dimensión Tecnológica - Componente Campus Virtual y Herramientas Tecnológicas (Docentes)",
        );

        //Categorias que tiene el instrumento para Estudiante
        $list_categorias1=array(
            "Componente Estudiantil"                    => "Dimensión Académica - Componente Estudiantil",
            "Componente Docencia"                       => "Dimensión Académica - Componente Docencia",
            "Componente Infraestructura Tecnológica"    => "Dimensión Tecnológica - Componente Plataforma e Infraestructura Tecnológica",
            "Componente Campus Virtual"                 => "Dimensión Tecnológica - Componente Campus Virtual y Herramientas Tecnológicas",
        );
        //Categorias que tiene el instrumento para Docentes
        $list_categorias2=array(
            "Componente Estudiantil"                    => "Dimensión Académica - Componente Estudiantil",
            "Componente Docencia"                       => "Dimensión Académica - Componente Docencia (Docentes)",
            "Componente Infraestructura Tecnológica"    => "Dimensión Tecnológica - Componente Plataforma e Infraestructura Tecnológica",
            "Componente Campus Virtual"                 => "Dimensión Tecnológica - Componente Campus Virtual y Herramientas Tecnológicas (Docentes)",
        );
        $list_categoriasComun=array(
            "Observaciones Generales"                   => "Observaciones Generales",
        );

        //Listado de instrumentos
        $list_instrumentos = array(
            array(
                "nombre"        =>"Evaluación Tecnopedagógica del EVA desde la Visión Estudiante",
                "nombre_corto"  =>"EVA Tecnopedagógico desde la Visión Estudiante",
                "descripcion"   =>"El presente instrumento está diseñado para realizar la Evaluación de los Entornos Virtuales de Aprendizaje del Campus Virtual-UCV dirigido a los estudiantes de la Universidad. Está conformado por tres (3) secciones a saber: Perfil de Usuario, Dimensión Académica, Dimensión Tecnológica; con un total de veintidós (22) interrogantes. Mediante este instrumento se determinará en qué nivel el EVA responde satisfactoriamente a cada uno de los aspectos evaluados desde las respectivas dimensiones. Con el objeto de medir y analizar el servicio que se presta a través de esta plataforma",
                "instrucciones" =>"El presente instrumento está diseñado para realizar la Evaluación de los Entornos Virtuales de Aprendizaje del Campus Virtual-UCV dirigido a los estudiantes de la Universidad. Está conformado por tres (3) secciones a saber: Perfil de Usuario, Dimensión Académica, Dimensión Tecnológica; con un total de veintidós (22) interrogantes. Mediante este instrumento se determinará en qué nivel el EVA responde satisfactoriamente a cada uno de los aspectos evaluados desde las respectivas dimensiones. Con el objeto de medir y analizar el servicio que se presta a través de esta plataforma"),

            array(
                "nombre"        =>"Evaluación Tecnopedagógica del EVA desde la Visión Docente",
                "nombre_corto"  =>"EVA Tecnopedagógico desde la Visión Docente",
                "descripcion"   =>"El presente instrumento está diseñado para realizar la Evaluación de los Entornos Virtuales de Aprendizaje del Campus Virtual-UCV dirigido a los docentes de la Universidad. Está conformado por tres (3) secciones a saber: Perfil de Usuario, Dimensión Académica, Dimensión Tecnológica; con un total de veintidós (22) interrogantes. Mediante este instrumento se determinará en qué nivel el EVA responde satisfactoriamente a cada uno de los aspectos evaluados desde las respectivas dimensiones. Con el objeto de medir y analizar el servicio que se presta a través de esta plataforma",
                "instrucciones" =>"El presente instrumento está diseñado para realizar la Evaluación de los Entornos Virtuales de Aprendizaje del Campus Virtual-UCV dirigido a los docentes de la Universidad. Está conformado por tres (3) secciones a saber: Perfil de Usuario, Dimensión Académica, Dimensión Tecnológica; con un total de veintidós (22) interrogantes. Mediante este instrumento se determinará en qué nivel el EVA responde satisfactoriamente a cada uno de los aspectos evaluados desde las respectivas dimensiones. Con el objeto de medir y analizar el servicio que se presta a través de esta plataforma")
        );

        //Creamos los indicadores
        foreach($indicadores as $Grupoindicadores){
            foreach($Grupoindicadores as $indicador => $tipo){
                $indicador = Indicador::firstOrCreate(['nombre' => $indicador, 'tipo' => $tipo]);  
            }
        }
        //Creamos las categorías
        foreach($categoriasTodas as $indexKey => $GrupoCategorias){
            foreach($GrupoCategorias as $nombreCorto => $categoria){
                $actual = Categoria::firstOrCreate(['nombre' => $categoria,'nombre_corto' => $nombreCorto]);
                $cantidad = 100/count($indicadores[$indexKey]);

                if ($actual->nombre == "Dimensión Perfil de Usuario") {
                    $actual->opciones = json_encode(array("perfil" => true, "likert" => array("Si","No") )); 
                    $actual->save();
                }

                if($actual->nombre == "Dimensión Perfil de Usuario" || $actual->nombre == "Observaciones Generales"){
                    $cantidad = 0;
                }
                
                //Asociamos los indicadores
                foreach($indicadores[$indexKey ] as $indicador => $tipo){
                    $indicador = Indicador::where(['nombre' => $indicador])->first() ; 
                    $actual->indicadores()->attach($indicador, ['valor_porcentual'=> $cantidad]);
                }
                
                if($actual->nombre == "Dimensión Tecnológica - Componente Campus Virtual y Herramientas Tecnológicas (Docentes)") {
                    $actual->indicadores()->detach();
                    $cantidad = 100/ (count($indicadores[4]) + count($indicadores[6]));
                    foreach($indicadores[4] as $indicador => $tipo){
                        $indicador = Indicador::where(['nombre' => $indicador])->first() ; 
                        $actual->indicadores()->attach($indicador, ['valor_porcentual'=> $cantidad]);
                    }
                    foreach($indicadores[6] as $indicador => $tipo){
                        $indicador = Indicador::where(['nombre' => $indicador])->first() ; 
                        $actual->indicadores()->attach($indicador, ['valor_porcentual'=> $cantidad]);
                    }
                }

                //Observaciones Generales
                if($indexKey != 0 && $indexKey != 7){
                    foreach($indicadores[7] as $indicador => $tipo){
                        $indicador = Indicador::where(['nombre' => $indicador])->first() ; 
                        $actual->indicadores()->attach($indicador);
                    }
                }

            }
        }

        //Creamos los instrumentos
        $rol_estudiante = CursoParticipanteRol::where('cvucv_shortname','student')->first();
        $roles_docente  = CursoParticipanteRol::where('cvucv_shortname','!=','student')->get();
        foreach($list_instrumentos as  $instrumento){
            $instrumento = Instrumento::firstOrCreate([
                'nombre'        => $instrumento['nombre'],
                'nombre_corto'  => $instrumento['nombre_corto'],
                'descripcion'   => $instrumento['descripcion'],
                'instrucciones' => $instrumento['instrucciones'],
                ]);

            if ($instrumento->nombre == "Evaluación Tecnopedagógica del EVA desde la Visión Estudiante") {
                $cantidad = 0;
                foreach($categoriasTodas[0] as $categoria){
                    $categoria = Categoria::where(['nombre' => $categoria])->first() ; 
                    $instrumento->categorias()->attach($categoria, ['valor_porcentual'=> 0]);
                }
                $cantidad = 100/count($list_categorias1);
                foreach($list_categorias1 as $categoria){
                    $categoria = Categoria::where(['nombre' => $categoria])->first() ; 
                    $instrumento->categorias()->attach($categoria, ['valor_porcentual'=> $cantidad]);
                }
                foreach($list_categoriasComun as $categoria){
                    $categoria = Categoria::where(['nombre' => $categoria])->first() ; 
                    $instrumento->categorias()->attach($categoria, ['valor_porcentual'=> 0]);
                }
                if(!empty($rol_estudiante)){
                    $instrumento->roles_dirigido()->attach($rol_estudiante);
                }
            }else if($instrumento->nombre == "Evaluación Tecnopedagógica del EVA desde la Visión Docente") {
                $cantidad = 0;
                foreach($categoriasTodas[0] as $categoria){
                    $categoria = Categoria::where(['nombre' => $categoria])->first() ; 
                    $instrumento->categorias()->attach($categoria, ['valor_porcentual'=> 0]);
                }
                $cantidad = 100/count($list_categorias2);
                foreach($list_categorias2 as $categoria){
                    $categoria = Categoria::where(['nombre' => $categoria])->first() ; 
                    $instrumento->categorias()->attach($categoria, ['valor_porcentual'=> $cantidad]);
                    
                }
                foreach($list_categoriasComun as $categoria){
                    $categoria = Categoria::where(['nombre' => $categoria])->first() ; 
                    $instrumento->categorias()->attach($categoria, ['valor_porcentual'=> 0]);
                }
                if(!($roles_docente->isEmpty())){
                    foreach($roles_docente as $rol){
                        $instrumento->roles_dirigido()->attach($rol);
                    }
                }

                //Configuracion adicional del instrumento
                $instrumento->anonimo = false;
                $instrumento->formato_evaluacion = true;
                $instrumento->invitacion_automatica = false;
                $instrumento->save();
            }
            
                
        }

    }
}
