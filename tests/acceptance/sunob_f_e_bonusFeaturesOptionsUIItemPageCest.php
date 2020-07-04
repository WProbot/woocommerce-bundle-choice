<?php 

class sunob_f_e_bonusFeaturesOptionsUIItemPageCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // // tests
    // public function tryToTest(AcceptanceTester $I)
    // {
    // }

    public function checkIfVisible(AcceptanceTester $I) {

		if( !$I->test_allowed_in_this_environment("sunob_f_") ) {
            return;
        }

        // go to the page
        $I->amOnPage('/product/test-diamond-1/');
        
        // check if main customize title is visible 
        $I->see('CUSTOMIZE AS PER YOUR WISH');

        // TODO check if options are visible and they have the desired colors and border radius etc. that are set in admin
        
	}

    public function interactAndObserve(AcceptanceTester $I) {

        if( !$I->test_allowed_in_this_environment("sunob_f_") ) {
            return;
        }

        // // go to the page
        // $I->amOnPage('/product/test-diamond-1/');
        
        // // check if main customize title is visible 
        // $I->see('CUSTOMIZE AS PER YOUR WISH');
        
        // TODO check if options are interactable

        // TODO check if option changes are reflected where it should be e.g. if its reflecting the change on price or additional info tab or at other place where it should be.
        
    }

}
