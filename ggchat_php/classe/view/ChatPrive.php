<?php
namespace GGChat\classe;

use GGChat\classe\Page;
use GGChat\classe\dao\ChatPriveDAO;
use PDO;

class ChatPrive extends Page
{
  
    public $title;

    public function __construct() // Constructeur demandant 2 paramètres
    {
        parent::__construct();

        $this->title= 'Chat Prive';
    
    }
    
    public function chatCheck()
    {
        if (isset($_POST['f_id']))
        {
            $chatPriveDAO = new ChatPriveDAO();
            
            $textpg = pg_escape_string($_REQUEST['textGlobal']);
            $message_prive_contenu = htmlspecialchars($textpg);

            //error handler 
            //check empty fields 
            if (empty($message_prive_contenu))
            {
                header("location: chatPrive.php?=emptyInput&membre=".$_GET["membre"]);
                exit(); 
            }
            else
            {
                $prive_row = $chatPriveDAO->getIdWhereMembreUid($_GET["membre"]);
                
                if($prive_row['id'])
                {
                    $chatPriveDAO->insertMsgPrive($prive_row['id'],$message_prive_contenu,$_SESSION['u_id']);

                    header("location: chatPrive.php?=MsgSend&membre=".$_GET["membre"]);
                    exit(); 
                }
                else{
                    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
                    exit(); 
                }
            }
        }   
    }
    public function chatPrint()
    {
        if(isset($_GET["public_id"]) && isset($_GET["membre"]))
        {
            $this->chatPrintPriveLazy();
        }
        else if (isset($_GET["membre"]))
        {
            $this->chatPrintPrive();
        } 
    }
    private function chatPrintPriveLazy()
    {
        $chatPriveDAO = new ChatPriveDAO();
        $prive_row = $chatPriveDAO->getIdWhereMembreUid($_GET["membre"]);
        if($prive_row)
        {
            $lastMessage = $chatPriveDAO->getLastMessage($_SESSION["u_id"],$prive_row['id']);
            $islastMessagesNotSame = strcmp($lastMessage['public_id'],$_GET['public_id']);
            if($islastMessagesNotSame)
            {
                $lastOldMessageID = $chatPriveDAO->getMessageIdWithPublicId($_GET['public_id']);
                $lastNewMessageID = $chatPriveDAO->getMessageIdWithPublicId($lastMessage['public_id']);
                $lastMessages = $chatPriveDAO->getMessageBetweenId($_SESSION["u_id"],$prive_row['id'],$lastNewMessageID['id'],$lastNewMessageID['id']);

                foreach ($lastMessages as $rowMessage) 
                {
                    $membre = $chatPriveDAO->getMembreWhereId($rowMessage);
                
                    $profilePic='img_user/'.$membre['id'].'_img.png';
                    
                    if (file_exists ($profilePic))
                    {
                        $pic= "<img class='chatPic' src='$profilePic' alt='Profile picture'>";
                    }
                    else
                    {
                        $pic="<img class='chatPic' src='img/compte_img.png' alt='Profile picture'>" ;
                    }
                    $name = "<a>".$membre['membre_uid']."</a>";
                    $this->doc .= "<p id='".$rowMessage["public_id"]."'>".$name.$pic." : ".$rowMessage["message_prive_contenu"]."</p>"; 
                }
            }
        }
    }
    private function chatPrintPrive()
    {
        $chatPriveDAO = new ChatPriveDAO();
        $prive_row = $chatPriveDAO->getIdWhereMembreUid($_GET["membre"]);

        if($prive_row)
        {
            $tableau = $chatPriveDAO->getMsgPrive($prive_row['id'],$_SESSION['u_id']);

            $reversed = array_reverse($tableau);
            
            foreach ($reversed as $row) 
            {
                $data = $chatPriveDAO->getMembreEnvoyeur($row['membre_envoyeur_fkey']);
                            
                $profilePic='img_user/'.$data['id'].'_img.png';
                
                if (file_exists ($profilePic))
                {
                    $pic= "<img class='chatPic' src='$profilePic' alt='Profile picture'>";
                }
                else
                {
                    $pic="<img class='chatPic' src='img/compte_img.png' alt='Profile picture'>" ;
                }
                $name = "<a>".$data['membre_uid']."</a>";
                $this->doc .= "<p id='".$row["public_id"]."'>".$name.$pic." : ".$row["message_prive_contenu"]."</p>";
            }
        }
        else{
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
            exit(); 
        }
    }
    public function chatOpen()
    {

        $this->doc .= '<div class="chat" id="chat">';   
    }
    public function chatClose()
    {

        $this->doc.='</div>';    
    }
    public function chatInput()
    {
        $this->doc .= '<form class="globalChatInput" action="chatPrive.php?membre='.$_GET["membre"].'" method="POST" >
            <input type="text" name="textGlobal" id="txt_1" placeholder="Envoyer un message"  >
            <button type="submit" name="submit">Envoyer</button>
            <input name="f_id" type="hidden" value="msgSend">
            </form>';    
    }
}