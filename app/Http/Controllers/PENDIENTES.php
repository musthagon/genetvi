/*
            $curso = Curso::firstOrCreate(
                ['id'                   => $data['id'] ],
                ['cvucv_shortname'      => $data['shortname'],
                'cvucv_category_id'     => $data['category'],
                'cvucv_fullname'        => $data['fullname'],
                'cvucv_displayname'     => $data['displayname'],
                'cvucv_summary'         => $data['summary'],
                'cvucv_visible'         => $data['visible'],
                'cvucv_link'            => env("CVUCV_GET_SITE_URL")."/course/view.php?id=".$data['id']
                ]
            );*/

//2. Categorias
            
            
            $categorias = [];
            $index = $data['category'];
            do {
                $categoria = $this->cvucv_get_courses_categories($index)[0];
                
                /*$categoria_del_curso = CategoriaDeCurso::firstOrCreate(
                    ['id'                   => $categoria['id'] ],
                    ['cvucv_parent_id'      => $categoria['parent'],
                    'cvucv_name'            => $categoria['name'],
                    'cvucv_coursecount'     => $categoria['coursecount'],
                    'cvucv_visible'         => $categoria['visible'],
                    'cvucv_path'            => $categoria['path'],
                    'cvucv_link'            => env("CVUCV_GET_SITE_URL")."/moodle/course/index.php?categoryid=".$data['id']
                    ]
                );*/

                $index = $categoria['parent'];

                array_push($categorias, $categoria);

            } while ($categoria['parent'] != 0);

            dd($categorias);




            // The Regular Expression filter
        $reg_exUrl = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
       
        // The Text you want to filter for urls
        $text = '<p style="text-align:center;"><img style="vertical-align:middle;margin-left:auto;margin-right:auto;" src="https://campusvirtual.ucv.ve/webservice/pluginfile.php/23/coursecat/description/banner-medicina.jpg" alt="" height="135" width="725" /></p> <p style="text-align:center;"> </p> <p style="text-align:center;">Coordinador de EaD de Medicina: Prof. Mariano Fernández</p> <p style="text-align:center;">Administrador de Espacio Virtual de Medicina: Edmund Chia - correo: edmund.chia@ucv.ve</p>';

        // Check if there is a url in the text
        /*if(preg_match($reg_exUrl, $text, $url)) {
            $text = preg_replace($reg_exUrl,  $url[0]."?token=".$wstoken , $text);
        } else {
            echo $text;
        }*/


/************************************************************ */

//Cursso -> matriculaciones -> roles
                $cursos_de_la_categoria = $this->cvucv_get_category_courses('category',$categoria['id']);

                foreach($cursos_de_la_categoria as $data){

                    $curso = Curso::find($data['id']);
    
                    //1. Verificamos que existan los cursos
                    //Si no existe, hay que crearlo
                    if(empty($curso)){
                        $curso = new Curso;
                    }
                    $curso->id                  = $data['id'];
                    $curso->cvucv_shortname     = $data['shortname'];
                    $curso->cvucv_category_id   = $data['categoryid'];
                    $curso->cvucv_fullname      = $data['fullname'];
                    $curso->cvucv_displayname   = $data['displayname'];
                    $curso->cvucv_summary       = $data['summary'];
                    $curso->cvucv_visible       = $data['visible'];
                    $curso->cvucv_link          = env("CVUCV_GET_SITE_URL")."/course/view.php?id=".$data['id'];

                    $curso->save();
                    
                    $participantes_curso = $this->cvucv_get_participantes_curso($data['id']);

                    foreach($participantes_curso as $participante){
                    


                        //2. Verificamos que este matriculado en ese curso
                        $matriculacion = CursoParticipante::where('cvucv_user_id', $participante['id'])
                            ->where('cvucv_curso_id', $data['id'])
                            ->first();
                        //Si no esta, hay que matricularlo
                        if(empty($matriculacion)){

                            $matriculacion = new CursoParticipante;

                            $matriculacion->cvucv_user_id  = $participante['id'];
                            $matriculacion->cvucv_curso_id = $data['id'];
                            $matriculacion->user_sync      = false;
                            $matriculacion->curso_sync     = true;

                            $matriculacion->save();

                        }else{
                            //Ya esta syncronizada su data
                            if(!$matriculacion->curso_sync){
                                $matriculacion->curso_sync   = true;
                                $matriculacion->save();
                            }
                        }


                    }

                }