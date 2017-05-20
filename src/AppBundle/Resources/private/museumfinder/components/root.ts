
import Vue from 'vue';
import Component from 'vue-class-component';

@Component({
  template: '#component-root'
})
export default class Root extends Vue {

  constructor() {
    super();
    console.log("Root Constructor");
  }
}

