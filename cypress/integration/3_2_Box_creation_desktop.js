context("Box_creation_tests", () => {

    let Test_Organ = "TestOrganisation";
    let Test_user = "some.admin@boxwise.co";
    let Test_passwd = "admin";
    let Test_product = "Jeans Female";
    let Test_size = "S";
    let Test_location = "TestShop";
    let Test_number = "100";
    let Test_id;

    beforeEach(function() {
        cy.Login(Test_user,Test_passwd);
        //cy.get('a[class = menu_stock').should("be.visible]");
        cy.get("a[data-testid='organisationsDropdown']").click();
        cy.get("li[data-testid='organisationOption'] a")
        .invoke('text').get('a').contains(Test_Organ).click();
        cy.get("a[class=menu_stock]").last().click();
    })
    /*
    it('Prevent box creation without data (Admin)', () => {
        cy.get("a.new-page.item-add").click();
        cy.get("button[class='btn btn-submit btn-success").contains("Save and close").click();
        cy.get("div[id='qtip-1-content']").should("be.visible");
        cy.get("div[id='qtip-2-content']").should("be.visible");
        cy.get("div[id='qtip-3-content']").should("be.visible");
    })*/
    it('Create Box with data (Admin)', () => {
        cy.get("a.new-page.item-add").click();
        cy.get("span[id=select2-chosen-1]").click();
        cy.get("input[id='s2id_autogen1_search']").type(Test_product);
        cy.get("div[class='select2-result-label']").first().click();
        cy.get("input[id='field_items']").click().type("100");  
        cy.get("span[id=select2-chosen-2]").click();
        cy.get("input[id='s2id_autogen2_search']").type(Test_size);
        cy.get("div[class='select2-result-label']").first().click();
        cy.get("span[id=select2-chosen-3]").click();
        cy.get("input[id='s2id_autogen3_search']").type(Test_location);
        cy.get("div[class='select2-result-label']").first().click();
        //cy.get("div[class='select2-result-label']").first().click();
        cy.get("button[class='btn btn-submit btn-success").contains("Save and close").click();
        cy.get("span[class='number']").invoke("text").then(text => {const Test_id = text});
        cy.get("h2").contains("New box created with ID "+Test_id);
        //cy.get("h2").contains("New box created with ID "+Test_id+"(write this number in the top right of the box label).")
        cy.get("h2").contains("This box contains "+Test_number+" "+Test_product+" and is located in "+Test_location).should("be.visible");

        
    })
});
