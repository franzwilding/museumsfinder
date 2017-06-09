import {Injectable} from '@angular/core';
import {Http, Response} from "@angular/http";
import {Observable} from "rxjs/Observable";
import {Observer} from "rxjs/Observer";
import 'rxjs/add/operator/map';

export class Museum {
  public id : number = 0;
  public name : string = "";
  public rating : number = 0;
  public web : string = "";
  public matching : number = 0;
  public address : string = "";
  public open : boolean = false;

  constructor(data : any) {
    this.id = data.id;
    this.name = data.name;
    this.web = data.web;
    this.matching = data.matching;
    this.address = data.address;
    this.open = data.open;
  }

  public matchingClass() : string {
    return '';
  }
}

@Injectable()
export class Data {

  private findUrl : string = "/results";
  private feedbackUrl : string = "/feedback";

  public started : boolean = false;
  public countQuestions : number = 5;
  public currentQuestion : number = 0;
  public onCurrentQuestionChange : Observable<number>;
  private observer : Observer<number>;

  public categories : string[] = [];
  public districts : string[] = [];
  public tags : string[] = [];
  public uniqueness : number = 50;
  public searchText : string = '';

  public constructor(private http : Http) {
    this.onCurrentQuestionChange = new Observable(observer => { this.observer = observer; });
    this.onCurrentQuestionChange.subscribe();
  }

  public start() : void {
    this.started = true;
    this.observer.next(this.currentQuestion);
  }

  public restart() : void {
    this.started = true;
    this.clear();
    if(this.currentQuestion != 1) {
      this.currentQuestion = 1;
      this.observer.next(this.currentQuestion);
    }
  }

  public end() : void {
    this.started = false;
    this.clear();

    if(this.currentQuestion != 0) {
      this.currentQuestion = 0;
      this.observer.next(this.currentQuestion);
    }
  }

  public next() : void {
    this.currentQuestion = this.currentQuestion + 1;
    this.observer.next(this.currentQuestion);
  }

  public prev() : void {
    this.currentQuestion = this.currentQuestion -1 ;
    this.observer.next(this.currentQuestion);
  }

  public goTo(index : number) : void {
    this.currentQuestion = index;
    this.observer.next(this.currentQuestion);
  }

  public clear() : void {
    this.categories = [];
    this.districts = [];
    this.tags = [];
    this.uniqueness = 50;
    this.searchText = '';
  }

  public getSearchData() : any {
    return {
      categories : this.categories,
      districts : this.districts,
      tags : this.tags,
      uniqueness : this.uniqueness,
      searchText : this.searchText
    };
  }

  public find() : Promise<Museum[]> {

    return new Promise((resolve, reject) => {
      this.http.post(this.findUrl, this.getSearchData()).subscribe((response : Response) => {
        resolve(response.json().map((data : any) => { return new Museum(data); }));
      }, (response : Response) => { reject(); });
    });
  }

  public sendFeedback(museum : Museum) : Observable<any> {
    let data = this.getSearchData();
    data.rating = museum.rating;
    return this.http.post(this.feedbackUrl, data);
  }
}