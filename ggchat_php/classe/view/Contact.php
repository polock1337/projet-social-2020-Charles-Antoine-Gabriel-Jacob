<?php
namespace GGChat\classe\view;

use GGChat\classe\Page;
use GGChat\includes\Dbh;
use PDO;

class Contact extends Page
{
  
    public $title;

    public function __construct() // Constructeur demandant 2 paramètres
    {
        parent::__construct();

        $this->title= 'Contact';

    }
    public function tableComputer()
    {
        
        $DbhObject = new Dbh();

        $dbh = $DbhObject->getDbh();
        
        $sql = "SELECT * FROM membre";
        $comp = $dbh->query($sql);
        $tableau = $comp->fetchAll(PDO::FETCH_ASSOC);
        $this->doc .= '<table>
        <thead>
        <tr>
        <th>First name</th>
        <th>Last name</th>
        <th>Email</th>
        <th>Username</th>
        <th></th>
        </tr>
        </thead>
        <tbody>';
        foreach ($tableau as $row) 
        {
            
            
            $this->doc .= "<tr><td>". $row["membre_first"] .
            "</td><td>" . $row["membre_last"] ."</td><td>". $row["membre_email"] ."</td><td>".
             $row["membre_uid"] ."</td><td><a class=\"buttonPrivate\" href=\"ChatPrive.php?membre=".$row["membre_uid"]."\">Envoyer un message</a></td></tr>";

            
        }

        $this->doc .= '</tbody></table>';
       
    }
    


}