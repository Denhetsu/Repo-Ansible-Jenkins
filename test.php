<?php

function render_php($path)
{
    ob_start();
    include($path);
    $var=ob_get_contents(); 
    ob_end_clean();
    return $var;
}

class Test extends PHPUnit\Framework\TestCase {
    protected function setUp(): void
    {
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
        parent::tearDown();
    }

    public function testWebsiteWork(){
        $output = render_php('/var/www/html/index.php');
        $this->assertStringContainsString("LOGIN",$output);
    }

    public function testIfDatabaseConnectionWork()
    {
        ob_start();
        include('/var/www/html/index.php');
        ob_end_clean();

        $login = 'ProfDeMath';
        $this->assertNotNull($getConn());
    }

    public function testLoginNotEmpty()
    {
        $_POST['login'] = '';
        $_POST['password'] = '12345';
        $output = render_php('/var/www/html/index.php');
        $this->assertStringContainsString("Missing login",$output);
    }

    public function testPasswordNotEmpty()
    {
        $_POST['login'] = 'ProfDeMath';
        $_POST['password'] = '';
        $output = render_php('/var/www/html/index.php');
        $this->assertStringContainsString("Missing password",$output);
    } 

    public function testWithCorrectCredentials()
    {
        $_POST['login'] = 'ProfDeMath';
        $_POST['password'] = '12345';
        $output = render_php('/var/www/html/index.php');
        $this->assertStringContainsString("Bonjour monsieur ".$_POST['login'],$output);
    }

    public function testWithIncorrectCredentials()
    {
        $_POST['login'] = 'ProfDeMath';
        $_POST['password'] = 'invalid-password';
        $output = render_php('/var/www/html/index.php');
        $this->assertStringContainsString("Invalid credentials",$output);
    }

    public function testIfPasswordIsHashed()
    {
        ob_start();
        include('/var/www/html/index.php');
        ob_end_clean();

        $login = 'ProfDeMath';
        $this->assertNotEquals('12345',$getUser($login,$getConn())['password']);
        $this->assertEquals('1','2');
    }
}