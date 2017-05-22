import { NgMuseumfinderPage } from './app.po';

describe('ng-museumfinder App', () => {
  let page: NgMuseumfinderPage;

  beforeEach(() => {
    page = new NgMuseumfinderPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
