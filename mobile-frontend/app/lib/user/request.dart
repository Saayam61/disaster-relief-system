import 'package:app/main_layout.dart';
import 'package:app/models/request.dart';
import 'package:app/providers/request_provider.dart';
import 'package:app/request_form.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class RequestPage extends StatefulWidget {
  const RequestPage({super.key});

  @override
  State<RequestPage> createState() => _RequestPageState();
}

class _RequestPageState extends State<RequestPage> {

  @override
  void initState() {
    super.initState();
    Future.microtask(() =>
      Provider.of<RequestProvider>(context, listen: false).getUserRequests());
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<RequestProvider>(context);
    final requests = provider.requests;

    return MainLayout(
      child: SingleChildScrollView(
        child: Column(
          children: [
            // Title Card (like before)
            Card(
            margin: EdgeInsets.all(16),
            color: Colors.blueGrey,
            child: Padding(
              padding: const EdgeInsets.all(8.0),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.request_quote, color: Colors.white),
                  SizedBox(width: 10),
                  Text(
                    'Requests', 
                    style: TextStyle(
                      color: Colors.white
                    ),
                  )
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),
        
          // Form Page
          ElevatedButton.icon(
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.blue, // Button background
            ),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => RequestForm()),
              );
            },
            icon: Icon(Icons.add, color: Colors.white),
            label: Text("Add Request", style: TextStyle(color: Colors.white)),
          ),
          const SizedBox(height: 16),
        
          // Posts list (cards)
          requests.isEmpty
            ? Card(
                margin: EdgeInsets.all(16),
                elevation: 4,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                color: Colors.amber.shade50,
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Text(
                    "No Requests have been registered yet.",
                    style: TextStyle(
                      fontStyle: FontStyle.italic,
                      color: Colors.black,
                    ),
                  ),
                ),
              )
            : Column(
                children: requests.map((request) {
                  return Card(
                    margin: EdgeInsets.fromLTRB(16,8,16,16),
                    elevation: 4,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                    color: Colors.amber.shade50,
                    child: Padding(
                      padding: const EdgeInsets.all(8.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "Request Type: ${request.type}",
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                              color: Colors.brown.shade800,
                            ),
                          ),
                          SizedBox(height: 10),
                          buildRichText("📅 Date: ", getDate(request.createdAt)),
                          buildRichText("🏥 Relief Center: ", request.centerName),
                          buildRichText("🏥 Status: ", request.status),
                          buildRichText("📝 Description: ", request.description),
                          buildRichText("📦 Quantity: ", "${request.quantity} ${request.unit}"),
                          buildRichText("🔁 Urgency: ", (request.urgency)),
                          SizedBox(height: 12),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              if (request.status == "pending")
                              ElevatedButton.icon(
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.blue,
                                  foregroundColor: Colors.white,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                ),
                                icon: Icon(Icons.edit, color: Colors.white),
                                label: Text("Edit"),
                                onPressed: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (context) => RequestForm(existingRequest: request),
                                    ),
                                  );
                                },
                              ),
                              SizedBox(width: 12), 
                              if (request.status == "pending" || request.status == "rejected") 
                              ElevatedButton.icon(
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.red.shade400,
                                  foregroundColor: Colors.white,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                ),
                                icon: Icon(Icons.delete, color: Colors.white),
                                label: Text("Delete"),
                                onPressed: () async {
                                  bool confirmed = await showDialog(
                                    context: context,
                                    builder: (context) => AlertDialog(
                                      title: Text("Are you sure?"),
                                      content: Text("This will permanently delete the request."),
                                      actions: [
                                        ElevatedButton(
                                          style: ElevatedButton.styleFrom(backgroundColor: Colors.blueGrey),
                                          onPressed: () => Navigator.pop(context, false),
                                          child: Text("Cancel", style: TextStyle(color: Colors.white)),
                                        ),
                                        ElevatedButton(
                                          style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                                          onPressed: () => Navigator.pop(context, true),
                                          child: Text("Delete", style: TextStyle(color: Colors.white)),
                                        ),
                                      ],
                                    ),
                                  );
        
                                  if (confirmed) {
                                    try {
                                      await Provider.of<RequestProvider>(context, listen: false)
                                          .deleteRequest(request.id);
        
                                      ScaffoldMessenger.of(context).showSnackBar(
                                        SnackBar(content: Text("Request deleted successfully!")),
                                      );
                                    } catch (e) {
                                      ScaffoldMessenger.of(context).showSnackBar(
                                        SnackBar(content: Text("Error: Could not delete request")),
                                      );
                                    }
                                  }
                                },
                              ),
                            ],
                          ),                        
                        ],
                      ),
                    ),
                  );
                }).toList(),
              ),
          ],
        ),
      ),
    );
  }
  RichText buildRichText(String label, String value) {
    return RichText(
      text: TextSpan(
        style: TextStyle(color: Colors.grey.shade800),
        children: [
          TextSpan(
            text: label,
            style: TextStyle(fontWeight: FontWeight.bold),
          ),
          TextSpan(text: value),
        ],
      ),
    );
  }
}
