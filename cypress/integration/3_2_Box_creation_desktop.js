context("Box_creation_tests", () => {
    let Test_location = "TestWarehouse";
    let Test_number = "100";
    let Test_user;
    let Test_passwd;
    let Test_product;
    let Test_size;
    let Test_id;
    
    
    before(function() {
        cy.getCoordinatorUser().then(($result) => {
            Test_user = $result.testCoordinator;
            Test_passwd = $result.testPwd;
            });
    });
    
    beforeEach(function() {
        cy.LoginAjax(Test_user,Test_passwd,true);
        cy.visit('/');
        cy.get("a[class=menu_stock]").last().click();
        cy.get("a.new-page.item-add").click();

    })

    function selectRandomProduct(){
        cy.get("div[id='s2id_field_product_id']").click();
        cy.get("body").then($body => {
        cy.get("ul[class='select2-results'] li").first().click();})
        cy.get("div[id='s2id_field_product_id']").then((text) =>{
              Test_product = text.text().replace('Product','').trim();
          });
        };
    
        function pickSize(){
            cy.get("span[id='select2-chosen-2']").click();
            cy.get("body").then($body => {
            cy.get("ul[class='select2-results'] li").eq(1).click()});
            cy.get("span[id='select2-chosen-2']").then((text) =>{
                  Test_size = text.text().replace('Size','').trim();
              });
            };
    
    function CheckEmpty(){
        cy.get("span[id='select2-chosen-1']").contains("Please select").should("be.visible")
        cy.get("span[id='select2-chosen-2']").contains("Please select").should("be.visible")
        cy.get("span[id='select2-chosen-3']").contains("Please select").should("be.visible")
        cy.get("input[id='field_items']").should("be.empty");
        cy.get("textarea[id='field_comments']").should("be.empty");
        //Check buttons
        cy.get("button").contains("Save and close").should("be.visible")
        cy.get("button").contains("Save and new").should("be.visible")
        cy.get("a").contains("Cancel").should("be.visible")
    }
    /*
    it('3_2_1 Prevent box creation without data (Admin)', () => {
        CheckEmpty();
        cy.get("button[class='btn btn-submit btn-success").contains("Save and close").click();
        cy.get("div[id='qtip-1-content']").should("be.visible");
        cy.get("div[id='qtip-2-content']").should("be.visible");
        cy.get("div[id='qtip-3-content']").should("be.visible");
    })
    */
    it('3_2_2 Create Box with data (Admin)', () => {
        selectRandomProduct();
        pickSize();
        cy.get("input[id='field_items']").click().type("100");  
        cy.get("span[id=select2-chosen-3]").click();
        cy.get("input[id='s2id_autogen3_search']").type(Test_location);
        cy.get("div[class='select2-result-label']").click();
        cy.get("button[class='btn btn-submit btn-success").contains("Save and close").click();
        cy.url().should('contain',"action=stock_confirm")
        
    })
    it('3_2_3 Create Box with data(Save and new)', () => {
        cy.get("div[id='s2id_field_product_id']").click();
        cy.get("div[class='select2-result-label']").contains(Test_product).click();
        cy.get("input[id='field_items']").click().type("100");  
        cy.get("span[id=select2-chosen-2]").click();
        cy.get("div[class='select2-result-label']").contains(Test_size).click();
        cy.get("span[id=select2-chosen-3]").click();
        cy.get("div[class='select2-result-label']").contains(Test_location).click();
        cy.get("button[class='btn btn-submit btn-success").contains("Save and new").click();
        cy.get("h2").should('contain',"This box contains "+Test_number+" "+Test_product+" and is located in "+Test_location).should("be.visible");
        cy.get("h2").then((message) => {
            Test_id = message.text().split('ID').pop().split('(write')[0].trim()});
        CheckEmpty();

        
    })
    it('3_2_4 Create QR-code', () => {
        cy.get("a[class=menu_stock]").last().click();
        cy.get("input[class ='form-control input-sm']").type(Test_id).click();
        cy.get("span[class = 'fa fa-search']").click();
        cy.get("input[class='item-select']").click();
        cy.get("i[class='fa fa-print']").click();
        cy.url().should('contain','pdf');
        cy.url().should('contain','label');

    });
});
