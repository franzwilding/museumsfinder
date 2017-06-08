import { Component } from '@angular/core';
import {Data} from "../../app/data";

@Component({
  selector: 'question-3',
  templateUrl: './question3.component.html'
})
export class Question3 {

  tags : string[] = [
    'Barrierefrei',
    'Freier Eintritt',
  ];

  constructor(private data: Data) {}

  toggle(tag) : void {
    if(this.isSelected(tag)) {
      this.data.tags.splice(this.data.tags.indexOf(tag), 1);
    } else {
      this.data.tags.push(tag);
    }
  }

  isSelected(tag: string) : boolean {
    return this.data.tags.indexOf(tag) >= 0;
  }
}