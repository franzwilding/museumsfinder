import { Component } from '@angular/core';
import {Data, Museum} from "../../app/data";

@Component({
  selector: 'results',
  templateUrl: './results.component.html'
})
export class Results {

  loading : boolean = false;
  ratingRange : number[] = [1, 2, 3, 4, 5];
  result  : Museum[] = [];

  constructor(private data: Data) {
    this.data.onCurrentQuestionChange.subscribe(result => {
      if(result == data.countQuestions + 1) {
        setTimeout(() => {
          this.find();
        }, 300);
      }
    });
  }

  find() : void {
    this.result = [];
    this.loading = true;
    this.data.find().subscribe((result : Museum[]) => {
      this.result = result;
      this.loading = false;
    }, () => {
      alert("Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.");
      this.data.goTo(this.data.countQuestions);
    });
  }
}