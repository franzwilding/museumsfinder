import {Injectable} from '@angular/core';

@Injectable()
export class Data {

  public started : boolean = false;
  public currentQuestion : number = 0;

  public start() : void {
    this.started = true;
  }

  public end() : void {
    this.started = true;
  }

  public next() : void {
    this.currentQuestion = this.currentQuestion + 1;
  }

  public prev() : void {
    this.currentQuestion = this.currentQuestion -1 ;
  }

}