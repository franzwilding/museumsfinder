import {Injectable} from '@angular/core';

@Injectable()
export class Data {

  public started : boolean = false;
  public countQuestions : number = 5;
  public currentQuestion : number = 0;

  public start() : void {
    this.started = true;
  }

  public restart() : void {
    this.started = true;
    this.clear();
    this.currentQuestion = 1;
  }

  public end() : void {
    this.started = false;
    this.clear();
    this.currentQuestion = 0;
  }

  public next() : void {
    this.currentQuestion = this.currentQuestion + 1;
  }

  public prev() : void {
    this.currentQuestion = this.currentQuestion -1 ;
  }

  public clear() : void {

  }
}