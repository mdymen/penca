<?php

class Helpers_Parsers_Liga extends Helpers_Parsers_Parsers {

    public function htmlToArray($html) {
        try {

            $object = $this->html_to_obj($html);

            $lista = $object['children'][0]['children'];

            $partidos = array();
            $result = array();

            for ($i = 0; $i < count($lista); $i = $i + 1) {

                $partidos[$i] = $this->jugado($lista[$i]);
                $partidos[$i] = $this->isPlayed($partidos[$i]);

                if ($partidos[$i]['equipo1']['nome'] == null) {
                    $partidos[$i] = $this->jugadoSinVejaComoFoiONoJugado($lista[$i]);
                    $partidos[$i] = $this->isPlayed($partidos[$i]);
                }

            }
            
            $this->verify($partidos);
            $return['body'] = $partidos;

            return $return;
        }
        catch (VerificaPartidosParserException $v) {
            throw $v;
        }
    }


    public function noJugado($partido) {
        $result['data'] = $partido['children'][0]['children'][1]['content'];
        $result['hora'] = $partido['children'][0]['children'][2]['html'];
        $result['equipo1']['nome'] = $partido['children'][0]['children'][3]['children'][0]['children'][1]['html'];
        $result['equipo2']['nome'] = $partido['children'][0]['children'][3]['children'][2]['children'][2]['html'];
        $result['equipo1']['resultado'] = null; 
        $result['equipo2']['resultado'] = null; 

        return $result;
    }

    public function jugadoSinVejaComoFoiONoJugado($partido) {
        $result['data'] = $partido['children'][0]['children'][1]['content'];
        $result['hora'] = $partido['children'][0]['children'][2]['html'];
        $result['equipo1']['nome'] = $partido['children'][0]['children'][3]['children'][0]['children'][1]['html'];
        $result['equipo2']['nome'] = $partido['children'][0]['children'][3]['children'][2]['children'][2]['html'];
        $result['equipo1']['resultado'] = $partido['children'][0]['children'][3]['children'][1]['children'][0]['html'];
        $result['equipo2']['resultado'] = $partido['children'][0]['children'][3]['children'][1]['children'][2]['html'];

        return $result;
    }

    public function jugado($partido) {
        $partidos = $partido['children'][0]['children'][2]['children'][1]['children'];

        $result['data'] = $partido['children'][0]['children'][1]['content'];
        $result['hora'] = $partido['children'][0]['children'][2]['children'][0]['html'];
        $result['equipo1']['nome'] = $partidos[0]['children'][1]['html'];
        $result['equipo1']['resultado'] = $partidos[1]['children'][0]['html'];
        $result['equipo2']['resultado'] = $partidos[1]['children'][2]['html'];
        $result['equipo2']['nome'] = $partidos[2]['children'][2]['html'];

        return $result;
    }
}