<?php 

class f_g_homeCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // // tests
    // public function tryToTest(AcceptanceTester $I)
    // {
    // }

    protected function configureButtonPositionForHome(AcceptanceTester $I) {

        if( !$I->test_allowed_in_this_environment("f_") ) {
            return;
        }

        //login to admin panel, assumed from the last tests since the webdriver maintains previous session. 

        /* buttons tab */
        // go to the page
        $I->amOnPage('/wp-admin/admin.php?page=eowbc-configuration');
        // echo $I->grabPageSource();
        
        // go to the tab
        // $I->click('General');
        $I->click('Buttons');
        $I->see('Choose where you want to display');

        // select button position
        // // $I->selectOption('form select[name=config_buttons_page]', 'Premium');
        // // $I->fillField('config_buttons_page', '1');    
        // $I->click('#config_buttons_page_dropdown_div');
        // $I->click('Home page only'); //('#config_buttons_page_dropdown_div > div.menu.transition.visible > div:nth-child(2)');
        $I->executeJS("jQuery('#buttons_page_dropdown_div').dropdown('set selected', 1);");  //better than setting 1 directly is to select the nth element that has value 1 

        $I->executeJS('window.scrollTo( 0, 300 );');        //$I->scrollTo('Save'); 
        $I->wait(3);

        // save 
        $I->click('Save');

        // confirm if saved properly or not
        $I->reloadPage();   //reload page
        $I->click('Buttons');
        $I->see('Home page only'); //that is the position option selected
    }

    public function testChoiceButtonWidgetOnHome(AcceptanceTester $I) {
		
        if( !$I->test_allowed_in_this_environment("f_") ) {
            return;
        }

        $this->configureButtonPositionForHome($I);

    	// go to the home page
		$I->amOnPage('/');

        $I->executeJS('window.scrollTo( 0, 300 );');      
        $I->wait(3);

		// Check if buttons with text x are visible 
        $I->see('Start with Diamond');

		// I click on button one and I see in next page text like 1 {button text}
        $I->click('Start with Diamond');
        $I->waitForText('CHOOSE A', 10);    
        $I->see('1');
        $I->see('DIAMOND');
        $I->see('2');
        $I->see('PREVIEW');

        //go back to the home page
        $I->moveBack();

        $I->executeJS('window.scrollTo( 0, 300 );');        //$I->scrollTo('Save'); 
        $I->wait(3);

		// I click on button two  and I see in next page text like 1 {button text} of second Button
        $I->click('Start with Setting');
        $I->waitForText('CHOOSE A', 10);    
        $I->see('1');
        $I->see('SETTING');
        $I->see('2');
        $I->see('PREVIEW');

	}

    public function testChoiceButtonWidgetBasedOnShortcode(AcceptanceTester $I) {
        
        if( !$I->test_allowed_in_this_environment("f_") ) {
            return;
        }
        
        // TODO - HIGH PRIORITY Since more users of our plugin are using going to use and rely on shortcode

    }

}
