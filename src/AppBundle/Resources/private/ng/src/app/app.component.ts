import {Component, ElementRef} from '@angular/core';
import {Data} from "./data";

@Component({
  selector: 'app',
  templateUrl: './app.component.html'
})
export class AppComponent {

  constructor(private data : Data, private me: ElementRef) {}

  ngAfterViewInit() {
    this.data.availableCategories = JSON.parse(this.me.nativeElement.dataset.categories);
    this.data.availableTags = JSON.parse(this.me.nativeElement.dataset.tags);
  }

  getClasses() : string {
    let classes = ['app-inner'];
    if(this.data.started) {
      classes.push('started');
    }
    return classes.join(' ');
  }

  getQuestionsOffset() : string {
    return (- (this.data.currentQuestion) * 100) + 'vw';
  }

  start() : void {
    if(!this.data.started) {
      this.data.start();
      setTimeout(() => {
        this.data.goTo(1);
      }, 300);
    }
  }

  end() : void {
    if(this.data.started) {
      if(confirm('Aktuelle Suche abbrechen und zur Startseite zurückkehren?')) {
        this.data.end();
      }
    }
  }

  restart() : void {
    if(this.data.started) {
      if(confirm('Soll eine neue Suche gestartet werden?')) {
        this.data.restart();
      }
    }
  }
}
