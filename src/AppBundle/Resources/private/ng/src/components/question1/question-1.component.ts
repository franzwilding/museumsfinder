import { Component } from '@angular/core';
import {Data} from "../../app/data";

@Component({
  selector: 'question-1',
  templateUrl: './question1.component.html'
})
export class Question1 {

  categories : string[] = [
    'Kunst',
    'Natur',
    'Technik',
    'Literatur',
    'Wien'
  ];

  constructor(private data: Data) {}

  toggle(category) : void {
    if(this.isSelected(category)) {
      this.data.categories.splice(this.data.categories.indexOf(category), 1);
    } else {
      this.data.categories.push(category);
    }
  }

  isSelected(category : string) : boolean {
    return this.data.categories.indexOf(category) >= 0;
  }
}