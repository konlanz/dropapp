context("Box_creation_tests", () => {
    beforeEach(function() {
        cy.Login("some.admin@boxwise.co","admin");
        //cy.get('a[class = menu_stock').should("be.visible]");
        cy.get("a[data-testid='organisationsDropdown']").click();
        cy.get("li[data-testid='organisationOption'] a")
        .invoke('text').get('a').contains("Test R4R").click();
        cy.get("a[class=menu_stock]").last().click();
    })
    
    it('Prevent box creation without data (Admin)', () => {
        cy.get("a.new-page.item-add").click();
        cy.get("button[class='btn btn-submit btn-success").contains("Save and close").click();
        cy.get("div[id='qtip-1-content']").should("be.visible");
        cy.get("div[id='qtip-2-content']").should("be.visible");
        cy.get("div[id='qtip-3-content']").should("be.visible");
    })
    it('Create Box with data (Admin)', () => {
        cy.get("a.new-page.item-add").click();
        cy.get("span[id=select2-chosen-1]").click();
        cy.get("input[id='s2id_autogen1_search']").type("Accessories");
        cy.get("div[class='select2-result-label']").first().click();
        cy.get("span[id=select2-chosen-2]").click();
        cy.get("div[class='select2-result-label']").first().click();
        cy.get("span[id=select2-chosen-3]").click();
        cy.get("div[class='select2-result-label']").first().click();
        cy.get("button[class='btn btn-submit btn-success").contains("Save and close").click();
        cy.get("h2").contains("New box created with ID").should("be.visible");
        
    })
});
