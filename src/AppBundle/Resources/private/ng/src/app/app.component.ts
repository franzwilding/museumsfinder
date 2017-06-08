import { Component } from '@angular/core';
import {Data} from "./data";

@Component({
  selector: 'app',
  templateUrl: './app.component.html'
})
export class AppComponent {

  constructor(private data : Data) {}

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
        this.data.currentQuestion = 1;
      }, 300);
    }
  }

  end() : void {
    if(this.data.started) {
      if(confirm('Aktuelle Suche abgebrochen und zur Startseite zurückkehren?')) {
        this.data.end();
      }
    }
  }
}
