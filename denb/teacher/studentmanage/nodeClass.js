class Item{
    prev;
    next;
    value;
    name;
    constructor(data,stuname){
        this.prev = null;
        this.next = null;
        this.value = data;
        this.name = stuname;
    }

}
class masaList{
    static head;
    constructor(){
        this.head = null;
    }
    add(value,name){
        const node = new Item(value,name);

        //先頭がNULLだったら追加
        if(this.head==null){
            this.head = node;
            return 0;
        }
        
        var current = this.head;

        //要素がヘッドのみのとき  
        if(current.next==null){
            //受け取った値がヘッドの値より小さい先頭に挿入
            if(current.value > value){
                this.head = node;
                node.next = current;
                current.prev = node;
                return 0;

            }
            else{//受け取った熱尾がヘッドの値より大きい2番目に挿入
                current.next = node;
                node.prev = current;
                return 0;
            }
        }
        var work
        //現在のノードのが最後のノードになるまで
        while(current!=null){
            //現在のノードが最後のノード
            if(current.next==null){
                if(current.value>value){
                    work = current.prev;
                    current.prev = node;
                    node.next = current;
                    work.next = node;
                    node.prev = work;
                }
                else{
                    current.next = node;
                    node.prev = current;
                }
                return 0;
            }
            //現在のノードの値が受け取った値より大きい
            if(current.value > value){
                //先頭に挿入
                if(current === this.head){
                    this.head = node;
                    node.next = current;
                    current.prev = node;
                    return 0;
                }
                work = current.prev;
                current.prev = node;
                node.next = current;
                node.prev = work;
                work.next = node;
                return 0;
            }
            current = current.next;
            
        }
        current.next = node;
        current.next.prev = current;
    }             
}


