<?php
//require_once(dirname(__FILE__) . '/../config.php');
//require_once('PHPMailer.class.php');
//require_once('Smtp.class.php');
class XieZhiMail
{
    private $_phpmail;
    private $_host;
    private $_port;
    private $_charset;
    private $_subje;
  /*
   * 构造函数
   *
   * @param 邮件发送者地址 $sender_email
   * @param 邮件发送者名字 $sender_name
   */
    function __construct($sender_email = 'data_monitor@sohu.com' , $sender_name = 'LC风格网', $username = '', $passwd = '', $host = '', $charset = 'utf-8')
    {
        $this->_phpmail = new Tool_Email_PHPMailer();
        $this->_phpmail->IsSMTP();
        $this->_phpmail->do_debug = 2;
        $this->_phpmail->SMTPAuth = true;
        $this->_phpmail->Username = $username;
        $this->_phpmail->Password = $passwd;
        $this->_phpmail->Host = $host;
        $this->_phpmail->Port = 25;
        $this->_phpmail->CharSet = $charset;
        $this->_phpmail->IsHTML(true);
        $this->_phpmail->SetFrom($sender_email, $sender_name);
    }
    function __destruct(){}

    public function setOptions($options)
    {
        if (isset($options['subject']))
        {
            $this->_phpmail->Subject = $options['subject'];
        }
        if (isset($options['altbody']))
        {
            $this->_phpmail->AltBody = $options['altbody'];
        }
        if (isset($options['message']))
        {
            $this->_phpmail->MsgHTML($options['message']);
        }
    }

    public function addAddress($email,$name)
    {
        $this->_phpmail->AddAddress($email,$name);
    }

    public function send()
    {
        return $this->_phpmail->Send();
    }

    public function getErrorInfo()
    {
        return $this->_phpmail->ErrorInfo;
    }
}


function send_noti_mail($to, $subject, $message, $nick = ''){
    $i = new XieZhiMail(MAIL_NAME, 'data_monitor', MAIL_NAME, MAIL_PASS, MAIL_SMTP);
    $i->setOptions(Array('subject' => $subject, 'message' => $message));
    $i->addAddress($to, $nick);
    $i->send();
}


//send_noti_mail("netyang@gmail.com", "hello", "hello");
