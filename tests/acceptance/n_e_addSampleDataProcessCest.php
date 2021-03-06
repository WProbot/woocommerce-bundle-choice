<?php 

class n_e_addSampleDataProcessCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // // tests
    // public function tryToTest(AcceptanceTester $I)
    // {
    // }

    public function step1(AcceptanceTester $I)
    {
    	if( !$I->test_allowed_in_this_environment("n_") ) {
            return;
        }

        // TODO temporarily skipped this test. must enable asap after fixing the relataed to step1 post event on travis. 
        return; 

        //
        // Go to step1   
        //
        $I->amOnPage('/wp-admin/admin.php?page=eowbc-configuration');
		
		// verify the tab
		$I->see('This section will help you add sample data and configurations');

		// start sample data insert process  
		$I->click('Click here for automated configuration and setup');

		$I->waitForText('You are at step 1 of 3 steps', 10);

		//
        // check/uncheck checkboxes of category 
		//
		// TODO right now keeping all the categories but in future we should remove some random categories and than after check confirm if the removed category is not available on the woocommerce categories page 

        $I->scrollTo('//*[@id="wpbody-content"]/div[2]/div[2]/form/div/table/tfoot/tr/td[1]/input');
        $I->wait(3);

        // click continue
        // $I->click('Create sample catagorie(s)');
        $I->click('//*[@id="wpbody-content"]/div[2]/div[2]/form/div/table/tfoot/tr/td[1]/input');    //('Create sample catagorie(s)');
 //        $I->executeJS('jQuery("#wpbody-content > div.ui.segment.container > div.wrap.woocommerce > form > div > table > tfoot > tr > td:nth-child(1) > input").trigger("click");');
 // //don't know why but the codeception click is not working here so now trying with JS

		$I->waitForText('You are at step 2 of 3 steps', 10);

    }

    //// for debugging purpose only
    // public function check_categories(AcceptanceTester $I)
    // {
    //     if( !$I->test_allowed_in_this_environment("n_") ) {
    //         return;
    //     }

    //     //
    //     // Go to step1   
    //     //
    //     $I->amOnPage('/wp-admin/edit-tags.php?taxonomy=product_cat&post_type=product');
        
    //     // take snap by failing
    //     $I->see('dsfsfsdfdsfdf');

    // }

    public function step2(AcceptanceTester $I)
    {
    	if( !$I->test_allowed_in_this_environment("n_") ) {
            return;
        }

        // TODO temporarily skipped this test. must enable asap after fixing the relataed to step1 post event on travis. 
        return;

		// verify that on 2nd step 
		$I->see('You are at step 2 of 3 steps');

		//
        // check/uncheck checkboxes of attributes 
		//
		// TODO right now keeping all the attributes but in future we should remove some random attributes and than after check confirm if the removed attribute is not available on the woocommerce attributes page 

		$I->scrollTo('//*[@id="wpbody-content"]/div[2]/div[2]/form/div/table/tfoot/tr/td[1]/input', -300, -100);
		$I->wiat(3);

        // click continue
        $I->click('//*[@id="wpbody-content"]/div[2]/div[2]/form/div/table/tfoot/tr/td[1]/input');   //('Create sample attribute(s)');

		$I->waitForText('You are at step 2 of 3 steps', 10);

    }

    public function step3(AcceptanceTester $I)
    {
    	if( !$I->test_allowed_in_this_environment("n_") ) {
            return;
        }

        // TODO temporarily skipped this test. must enable asap after fixing the relataed to step1 post event on travis. 
        return;
        
		// verify that on 3rd step 
		$I->see('You are at step 3 of 3 steps');

		// TODO in future if it helps we should check the speed of insert as well as if the ajax progress of inserted product count is showing or not

        $I->scrollTo('//*[@id="wpbody-content"]/div[2]/div[2]/form/div/table/tfoot/tr/td[1]/input', -300, -100);
        $I->wiat(3);

        // click continue
        $I->click('//*[@id="wpbody-content"]/div[2]/div[2]/form/div/table/tfoot/tr/td[1]/input');   //('Create sample product(s)');

        // wait for all 120 products to be inserted and than redirct to home tab
		$I->waitForText('This section will help you add sample data and configurations', 300);

    }

}
