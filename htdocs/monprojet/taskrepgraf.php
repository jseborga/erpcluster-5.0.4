<?php

require_once DOL_DOCUMENT_ROOT .'/monprojet/jpgraph/src/jpgraph.php';
require_once DOL_DOCUMENT_ROOT .'/monprojet/jpgraph/src/jpgraph_line.php';
require_once DOL_DOCUMENT_ROOT .'/monprojet/jpgraph/src/jpgraph_bar.php';
require_once DOL_DOCUMENT_ROOT .'/monprojet/jpgraph/src/jpgraph_pie.php';
require_once DOL_DOCUMENT_ROOT .'/monprojet/jpgraph/src/jpgraph_pie3d.php';

class Graficos 
{
    
    public function __construct(){
    }
    
    public function graficar_lineas($data,$labels,$title,$titlex,$titley) 
    {
        $ofi1 = $data[1]['ofi'];
        $legend1 = $data[1]['legend'];
        $color1 = $data[1]['color'];

        $ofi2 = $data[2]['ofi'];
        $legend2 = $data[2]['legend'];
        $color2 = $data[2]['color'];

        //$ofi1 = array(100,300,250,200,350);
        //$ofi2 = array(50,250,300,320,200);
        //$labels=array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo');
        
        $grafico = new Graph(940,400,'auto');
        $grafico->SetScale("textlin");
        $grafico->SetMargin(50,30,30,50);
        //$grafico->title->Set("GASTOS POR MES");
        $grafico->title->Set($title);
        $grafico->xaxis->title->Set($titlex);
        $grafico->xaxis->SetTickLabels($labels);
        $grafico->yaxis->title->Set($titley);
                
        $grafico->xgrid->Show();
        $grafico->xgrid->SetLineStyle("solid");
        $grafico->xgrid->SetColor('#E3E3E3');
        
        $p1 = new LinePlot($ofi1);
        $grafico->Add($p1);
        $p1->value->Show();
        $p1->SetColor("#6495ED");
        $p1->SetLegend($legend1);
        
        $p2 = new LinePlot($ofi2);
        $grafico->Add($p2);
        $p2->value->Show();
        $p2->SetColor("#B22222");
        $p2->SetLegend($legend2);
        
        $grafico->legend->SetFrameWeight(1);
        
        $grafico->title->SetFont(FF_FONT1,FS_BOLD);
        $grafico->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $grafico->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
        
        $grafico->Stroke();
    }
    
    public function graficar_barras() 
    {
        $data1y = array(100,300,250,200,350);
        $data2y = array(50,250,300,320,200);
        $labels=array('Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes');
        
        $grafico = new Graph(940,400,'auto');
        $grafico->SetScale("textlin");
        $grafico->SetMargin(50,30,30,40);
        $grafico->title->Set("VS. GASTOS DIA SEMANA PASADA/DIA SEMANA ACTUAL");
        $grafico->xaxis->title->Set("MES");
        $grafico->xaxis->SetTickLabels($labels);
        $grafico->yaxis->title->Set("GASTOS");
        
        $b1plot = new BarPlot($data1y);
        $b2plot = new BarPlot($data2y);
        
        $gbplot = new GroupBarPlot(array($b1plot,$b2plot));
        $grafico->Add($gbplot);
        
        $b1plot->value->Show();
        $b1plot->SetColor("white");
        $b1plot->SetFillColor("#B0C4DE");
        $b1plot->SetWidth(50);
        $b2plot->value->Show();
        $b2plot->SetColor("white");
        $b2plot->SetFillColor("#11CCCC");
        $b2plot->SetWidth(50);
        
        $grafico->title->SetFont(FF_FONT1,FS_BOLD);
        $grafico->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $grafico->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
        
        $grafico->Stroke();
    }
    
    public function graficar_pastel() 
    {
        $datos = array(100,300);

        $grafico = new PieGraph(940,400,'auto');
        $grafico->SetScale("textlin");
        $grafico->SetMargin(50,30,30,40);
        
        $tema= new VividTheme;
        $grafico->SetTheme($tema);

        $grafico->title->Set("% HOMBRES y MUJERES");

        $p1 = new PiePlot3D($datos);
        $grafico->Add($p1);

        $p1->ShowBorder();
        $p1->SetColor('white');
        $p1->ExplodeSlice(1);
        $grafico->Stroke();
    }
}
?>